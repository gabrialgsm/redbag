<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
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

        return view('volunteer.requests', compact('requests'));
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
}
