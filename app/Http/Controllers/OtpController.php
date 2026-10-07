<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\OtpChallenge;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function show(OtpChallenge $challenge): View|RedirectResponse
    {
        if ($challenge->verified_at) {
            return redirect()->route('home')->with('success', 'এই OTP verification ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        return view('otp-verify', compact('challenge'));
    }

    public function resend(OtpChallenge $challenge, OtpService $otpService): RedirectResponse
    {
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
            BloodRequest::whereKey($challenge->reference_id)->update(['requester_phone_verified_at' => now()]);
            return redirect()->route('blood.request')->with('success', 'আপনার মোবাইল নম্বর verified হয়েছে। এখন আবেদনটি verification queue-তে আছে।');
        }

        return redirect()->route('home')->with('success', 'OTP verification সম্পন্ন হয়েছে।');
    }
}
