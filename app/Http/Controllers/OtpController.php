<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\Donor;
use App\Models\OtpChallenge;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function show(OtpChallenge $challenge, OtpService $otpService): View|RedirectResponse
    {
        if (!$otpService->deliveryConfigured()) {
            return redirect()->route('home')->with('error', 'মোবাইল OTP সেবা এখনো চালু হয়নি। এই মুহূর্তে নিবন্ধন বা আবেদন যাচাই করা যাচ্ছে না।');
        }

        if ($challenge->verified_at) {
            return redirect()->route('home')->with('success', 'এই OTP verification ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        return view('otp-verify', compact('challenge'));
    }

    public function resend(OtpChallenge $challenge, OtpService $otpService): RedirectResponse
    {
        $otpService->ensureDeliveryConfigured();

        if ($challenge->verified_at) {
            return redirect()->route('home')->with('success', 'এই OTP verification ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        $newChallenge = $otpService->send(
            $challenge->phone,
            $challenge->purpose,
            $challenge->reference_type,
            $challenge->reference_id
        );

        return redirect()->route('otp.show', $newChallenge)->with('success', 'নতুন OTP পাঠানোর অনুরোধ গ্রহণ করা হয়েছে।');
    }

    public function verify(Request $request, OtpChallenge $challenge, OtpService $otpService): RedirectResponse
    {
        $otpService->ensureDeliveryConfigured();

        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => '৬ সংখ্যার OTP দিন।',
            'code.digits' => 'OTP অবশ্যই ৬ সংখ্যার হতে হবে।',
        ]);

        $otpService->verify($challenge, $request->string('code')->toString());

        if ($challenge->purpose === OtpService::PURPOSE_DONOR_REGISTRATION) {
            $donor = Donor::findOrFail($challenge->reference_id);
            $donor->update(['phone_verified_at' => now()]);
            $request->session()->regenerate();
            $request->session()->put('donor_id', $donor->id);

            return redirect()->route('donor.dashboard')->with('success', 'আপনার মোবাইল নম্বর সফলভাবে verified হয়েছে। Donor dashboard থেকে availability সেট করুন।');
        }

        if ($challenge->purpose === OtpService::PURPOSE_BLOOD_REQUEST) {
            $bloodRequest = BloodRequest::findOrFail($challenge->reference_id);
            $bloodRequest->update(['requester_phone_verified_at' => now()]);

            BloodRequestEvent::create([
                'blood_request_id' => $bloodRequest->id,
                'actor_type' => 'requester',
                'event' => 'requester_phone_verified',
                'from_status' => $bloodRequest->status,
                'to_status' => $bloodRequest->status,
            ]);

            $trackingToken = $request->session()->pull('redbag_request_tracking_'.$bloodRequest->request_code);

            if ($trackingToken) {
                return redirect()->route('blood.request.track', [
                    'requestCode' => $bloodRequest->request_code,
                    'token' => $trackingToken,
                ])->with('success', 'ফোন নম্বর যাচাই হয়েছে। এই private link দিয়ে আবেদনটির অবস্থা দেখতে পারবেন।');
            }

            return redirect()->route('blood.request')->with('success', "আপনার মোবাইল নম্বর verified হয়েছে। Request ID: {$bloodRequest->request_code}।");
        }

        return redirect()->route('home')->with('success', 'OTP verification সম্পন্ন হয়েছে।');
    }
}
