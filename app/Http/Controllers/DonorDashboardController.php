<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\BloodRequestMatch;
use App\Models\Donor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DonorDashboardController extends Controller
{
    private function donor(Request $request): ?Donor
    {
        $id = $request->session()->get('donor_id');

        return $id ? Donor::find($id) : null;
    }

    public function show(Request $request): View|RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor || !$donor->phone_verified_at) {
            return redirect()->route('donor.register')->with('error', 'Donor dashboard দেখতে আগে phone verification সম্পন্ন করতে হবে।');
        }

        return view('donor-dashboard', compact('donor'));
    }

    public function requests(Request $request): View|RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor || !$donor->phone_verified_at) {
            return redirect()->route('donor.register')->with('error', 'Donor request inbox দেখতে আগে phone verification সম্পন্ন করতে হবে।');
        }

        $matches = BloodRequestMatch::query()
            ->with('bloodRequest')
            ->where('donor_id', $donor->id)
            ->whereIn('response', ['pending', 'maybe'])
            ->whereHas('bloodRequest', function ($query) {
                $query->whereIn('status', ['matching', 'matched'])
                    ->where(function ($active) {
                        $active->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            })
            ->latest()
            ->paginate(20);

        return view('donor-requests', compact('donor', 'matches'));
    }

    public function respond(Request $request, BloodRequestMatch $match): RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor || !$donor->phone_verified_at) {
            return redirect()->route('donor.register')->with('error', 'Donor request inbox দেখতে আগে phone verification সম্পন্ন করতে হবে।');
        }

        if ((int) $match->donor_id !== (int) $donor->id) {
            abort(404);
        }

        $data = $request->validate([
            'response' => ['required', 'in:accepted,declined,maybe'],
            'contact_consent' => ['accepted_if:response,accepted'],
        ], [
            'contact_consent.accepted_if' => 'Accept করার আগে RED BAG volunteer-এর মাধ্যমে যোগাযোগের সম্মতি দিন।',
        ]);

        DB::transaction(function () use ($match, $donor, $data): void {
            $lockedMatch = BloodRequestMatch::whereKey($match->id)->lockForUpdate()->firstOrFail();

            if ((int) $lockedMatch->donor_id !== (int) $donor->id
                || !in_array($lockedMatch->response, ['pending', 'maybe'], true)) {
                abort(409, 'এই donor request-এর response আর পরিবর্তন করা যাবে না।');
            }

            $bloodRequest = BloodRequest::whereKey($lockedMatch->blood_request_id)->lockForUpdate()->firstOrFail();

            if (!in_array($bloodRequest->status, ['matching', 'matched'], true)
                || ($bloodRequest->expires_at && $bloodRequest->expires_at->isPast())) {
                abort(409, 'এই request আর সক্রিয় নেই।');
            }

            $fromStatus = $bloodRequest->status;
            $contactConsentAt = $data['response'] === 'accepted' ? now() : null;

            $lockedMatch->update([
                'response' => $data['response'],
                'responded_at' => now(),
                'contact_consent_at' => $contactConsentAt,
            ]);

            if ($data['response'] === 'accepted' && $bloodRequest->status === 'matching') {
                $bloodRequest->update(['status' => 'matched']);
            }

            BloodRequestEvent::create([
                'blood_request_id' => $bloodRequest->id,
                'actor_type' => 'donor',
                'actor_identifier' => $donor->donor_code,
                'event' => match ($data['response']) {
                    'accepted' => 'donor_response_accepted',
                    'declined' => 'donor_response_declined',
                    'maybe' => 'donor_response_maybe',
                },
                'from_status' => $fromStatus,
                'to_status' => $bloodRequest->fresh()->status,
                'metadata' => ['response' => $data['response']],
            ]);
        });

        $message = match ($data['response']) {
            'accepted' => 'আপনার response নথিভুক্ত হয়েছে। RED BAG volunteer-এর মাধ্যমে যোগাযোগের সম্মতি সংরক্ষিত হয়েছে; আপনার নম্বর requester-কে প্রকাশ করা হয়নি।',
            'declined' => 'আপনার response নথিভুক্ত হয়েছে।',
            'maybe' => 'আপনার Maybe response নথিভুক্ত হয়েছে।',
        };

        return redirect()->route('donor.requests')->with('success', $message);
    }

    public function availability(Request $request): RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor || !$donor->phone_verified_at) {
            return redirect()->route('donor.register')->with('error', 'আপনার donor session পাওয়া যায়নি।');
        }

        $data = $request->validate([
            'availability' => ['required', 'in:available,maybe,unavailable'],
        ]);

        $donor->update(['availability' => $data['availability']]);

        return redirect()->route('donor.dashboard')->with('success', 'আপনার availability সফলভাবে আপডেট হয়েছে।');
    }

    public function profile(Request $request): RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor || !$donor->phone_verified_at) {
            return redirect()->route('donor.register')->with('error', 'আপনার donor session পাওয়া যায়নি।');
        }

        $data = $request->validate([
            'district' => ['required', 'string', 'max:80'],
            'area' => ['nullable', 'string', 'max:120'],
        ]);

        $donor->update($data);

        return redirect()->route('donor.dashboard')->with('success', 'আপনার location profile আপডেট হয়েছে।');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('donor_id');
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'আপনাকে RED BAG donor dashboard থেকে logout করা হয়েছে।');
    }
}
