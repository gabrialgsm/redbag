<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\BloodRequestMatch;
use App\Services\DonorMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VolunteerReviewController extends Controller
{
    public function index(): View
    {
        $requests = BloodRequest::query()
            ->where('status', 'pending_verification')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderBy('created_at')
            ->paginate(20);

        $approvedRequests = BloodRequest::query()
            ->whereIn('status', ['verified', 'matching', 'matched'])
            ->whereNotNull('requester_phone_verified_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->withCount('matches')
            ->withCount(['matches as accepted_matches_count' => fn ($query) => $query->where('response', 'accepted')->whereNotNull('contact_consent_at')])
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderBy('created_at')
            ->get();

        return view('volunteer.requests', compact('requests', 'approvedRequests'));
    }

    public function review(Request $request, BloodRequest $bloodRequest): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject,needs_info'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($bloodRequest->status !== 'pending_verification') {
            abort(409, 'এই আবেদনটি আর review queue-তে নেই।');
        }

        if (in_array($data['action'], ['reject', 'needs_info'], true)
            && mb_strlen(trim((string) ($data['reason'] ?? ''))) < 5) {
            throw ValidationException::withMessages([
                'reason' => 'Reject বা অতিরিক্ত তথ্যের অনুরোধের জন্য কমপক্ষে ৫ অক্ষরের কারণ লিখুন।',
            ]);
        }

        if ($data['action'] === 'approve' && !$bloodRequest->requester_phone_verified_at) {
            throw ValidationException::withMessages([
                'action' => 'আবেদনকারীর ফোন যাচাই সম্পন্ন না হওয়া পর্যন্ত আবেদন approve করা যাবে না।',
            ]);
        }

        $fromStatus = $bloodRequest->status;
        $event = match ($data['action']) {
            'approve' => 'request_approved',
            'reject' => 'request_rejected',
            'needs_info' => 'more_information_requested',
        };
        $toStatus = match ($data['action']) {
            'approve' => 'verified',
            'reject' => 'cancelled',
            'needs_info' => 'pending_verification',
        };

        DB::transaction(function () use ($bloodRequest, $request, $data, $fromStatus, $event, $toStatus): void {
            if ($data['action'] !== 'needs_info') {
                $bloodRequest->update(['status' => $toStatus]);
            }

            BloodRequestEvent::create([
                'blood_request_id' => $bloodRequest->id,
                'actor_type' => 'volunteer',
                'actor_identifier' => $request->session()->get('volunteer_email'),
                'event' => $event,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'reason' => trim((string) ($data['reason'] ?? '')) ?: null,
            ]);
        });

        $message = match ($data['action']) {
            'approve' => 'আবেদনটি volunteer review-এর পর approved হয়েছে। এটি donation নিশ্চিত হওয়ার প্রমাণ নয়।',
            'reject' => 'আবেদনটি reject করে বন্ধ করা হয়েছে।',
            'needs_info' => 'আবেদনকারীর কাছ থেকে অতিরিক্ত তথ্য প্রয়োজন হিসেবে নথিভুক্ত হয়েছে।',
        };

        return redirect()->route('volunteer.requests')->with('success', $message);
    }

    public function match(BloodRequest $bloodRequest, DonorMatchingService $matchingService): RedirectResponse
    {
        $result = $matchingService->shortlist($bloodRequest);

        if ($result['total'] === 0) {
            return redirect()->route('volunteer.requests')
                ->with('error', 'এই আবেদনটির জন্য একই blood group ও জেলায় ফোন-যাচাইকৃত, available donor পাওয়া যায়নি। কোনো notification পাঠানো হয়নি।');
        }

        return redirect()->route('volunteer.requests')->with(
            'success',
            $result['created'].' নতুন donor shortlist-এ যুক্ত হয়েছে; মোট '.$result['total'].' জন donor-এর private dashboard inbox-এ request দেখা যাবে। কোনো SMS/push notification পাঠানো হয়নি।'
        );
    }

    public function contacts(Request $request, BloodRequest $bloodRequest): View
    {
        abort_unless(
            $bloodRequest->requester_phone_verified_at
                && in_array($bloodRequest->status, ['verified', 'matching', 'matched'], true)
                && (!$bloodRequest->expires_at || $bloodRequest->expires_at->isFuture()),
            404
        );

        $acceptedMatches = $bloodRequest->matches()
            ->with('donor')
            ->where('response', 'accepted')
            ->whereNotNull('contact_consent_at')
            ->get();

        BloodRequestEvent::create([
            'blood_request_id' => $bloodRequest->id,
            'actor_type' => 'volunteer',
            'actor_identifier' => $request->session()->get('volunteer_email'),
            'event' => 'volunteer_contact_workspace_viewed',
            'from_status' => $bloodRequest->status,
            'to_status' => $bloodRequest->status,
            'metadata' => ['consented_donor_count' => $acceptedMatches->count()],
        ]);

        return view('volunteer.contacts', compact('bloodRequest', 'acceptedMatches'));
    }

    public function logContact(Request $request, BloodRequest $bloodRequest): RedirectResponse
    {
        abort_unless(
            $bloodRequest->requester_phone_verified_at
                && in_array($bloodRequest->status, ['verified', 'matching', 'matched'], true)
                && (!$bloodRequest->expires_at || $bloodRequest->expires_at->isFuture()),
            404
        );

        $data = $request->validate([
            'target' => ['required', 'in:requester,donor'],
            'outcome' => ['required', 'in:called,connected,unreachable,follow_up'],
            'match_id' => ['nullable', 'integer'],
        ]);

        $donorCode = null;

        if ($data['target'] === 'donor') {
            if (empty($data['match_id'])) {
                throw ValidationException::withMessages(['match_id' => 'Donor নির্বাচন করুন।']);
            }

            $match = $bloodRequest->matches()
                ->with('donor')
                ->whereKey($data['match_id'])
                ->where('response', 'accepted')
                ->whereNotNull('contact_consent_at')
                ->firstOrFail();

            $donorCode = $match->donor->donor_code;
        }

        BloodRequestEvent::create([
            'blood_request_id' => $bloodRequest->id,
            'actor_type' => 'volunteer',
            'actor_identifier' => $request->session()->get('volunteer_email'),
            'event' => 'volunteer_contact_logged',
            'from_status' => $bloodRequest->status,
            'to_status' => $bloodRequest->status,
            'metadata' => [
                'target' => $data['target'],
                'outcome' => $data['outcome'],
                'donor_code' => $donorCode,
            ],
        ]);

        return redirect()->route('volunteer.requests.contacts', $bloodRequest)
            ->with('success', 'Contact attempt-এর ফলাফল নথিভুক্ত হয়েছে। কোনো ফোন নম্বর audit log-এ রাখা হয়নি।');
    }
}
