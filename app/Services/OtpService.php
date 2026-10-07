<?php

namespace App\Services;

use App\Models\OtpChallenge;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public const PURPOSE_DONOR_REGISTRATION = 'donor_registration';
    public const PURPOSE_BLOOD_REQUEST = 'blood_request';

    public function send(string $phone, string $purpose, ?string $referenceType = null, ?int $referenceId = null): OtpChallenge
    {
        $challenge = OtpChallenge::where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if ($challenge && $challenge->created_at->gt(now()->subMinutes(2))) {
            throw ValidationException::withMessages([
                'phone' => 'নতুন OTP পাঠানোর আগে ২ মিনিট অপেক্ষা করুন।',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        $challenge = OtpChallenge::create([
            'phone' => $phone,
            'purpose' => $purpose,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        if (config('otp.driver', 'log') === 'log') {
            Log::info('RED BAG OTP', [
                'phone' => $phone,
                'purpose' => $purpose,
                'code' => $code,
                'challenge_id' => $challenge->id,
            ]);
        }

        return $challenge;
    }

    public function verify(OtpChallenge $challenge, string $code): void
    {
        if ($challenge->verified_at) {
            throw ValidationException::withMessages(['code' => 'এই OTP ইতিমধ্যে ব্যবহার করা হয়েছে।']);
        }

        if ($challenge->expires_at->isPast()) {
            throw ValidationException::withMessages(['code' => 'OTP-এর মেয়াদ শেষ হয়েছে। নতুন OTP নিন।']);
        }

        if ($challenge->attempts >= 5) {
            throw ValidationException::withMessages(['code' => 'অনেকবার ভুল OTP দেওয়া হয়েছে। নতুন OTP নিন।']);
        }

        $challenge->increment('attempts');

        if (!Hash::check($code, $challenge->code_hash)) {
            throw ValidationException::withMessages(['code' => 'OTP সঠিক নয়। আবার চেষ্টা করুন।']);
        }

        $challenge->update(['verified_at' => now()]);
    }
}
