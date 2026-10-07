<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BloodRequestController extends Controller
{
    public function store(Request $request, OtpService $otpService): RedirectResponse
    {
        $data = $request->validate([
            'requester_name' => ['required','string','min:2','max:120'],
            'requester_phone' => ['required','string','regex:/^01[3-9][0-9]{8}$/'],
            'patient_name' => ['nullable','string','max:120'],
            'blood_group' => ['required','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'units' => ['required','integer','min:1','max:20'],
            'urgency' => ['required','in:normal,urgent,emergency'],
            'hospital_name' => ['required','string','min:2','max:180'],
            'district' => ['required','string','max:80'],
            'area' => ['nullable','string','max:120'],
            'needed_at' => ['nullable','date'],
        ], [
            'requester_phone.regex' => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন।',
        ]);

        $bloodRequest = BloodRequest::create([
            ...$data,
            'request_code' => 'RB-'.strtoupper(Str::random(8)),
            'status' => 'pending_verification',
            'expires_at' => now()->addHours($data['urgency'] === 'emergency' ? 12 : 48),
        ]);

        $challenge = $otpService->send(
            $bloodRequest->requester_phone,
            OtpService::PURPOSE_BLOOD_REQUEST,
            BloodRequest::class,
            $bloodRequest->id
        );

        return redirect()->route('otp.show', $challenge)->with('success',
            "আপনার আবেদন গ্রহণ করা হয়েছে। Request ID: {$bloodRequest->request_code}। এখন মোবাইল OTP দিয়ে আবেদনটি verify করুন।"
        );
    }
}
