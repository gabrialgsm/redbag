<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Services\OtpService;
use App\Services\RequestTrackingTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BloodRequestController extends Controller
{
    public function store(Request $request, OtpService $otpService, RequestTrackingTokenService $trackingTokens): RedirectResponse
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

        // Fail closed before creating a request if no safe OTP delivery exists.
        $otpService->ensureDeliveryConfigured();

        $trackingToken = $trackingTokens->generate();
        [$bloodRequest, $challenge] = DB::transaction(function () use ($data, $otpService, $trackingTokens, $trackingToken): array {
            $bloodRequest = BloodRequest::create([
                ...$data,
                'request_code' => 'RB-'.strtoupper(Str::random(8)),
                'tracking_token_hash' => $trackingTokens->hash($trackingToken),
                'status' => 'pending_verification',
                'expires_at' => now()->addHours($data['urgency'] === 'emergency' ? 12 : 48),
            ]);

            BloodRequestEvent::create([
                'blood_request_id' => $bloodRequest->id,
                'actor_type' => 'requester',
                'event' => 'request_submitted',
                'from_status' => null,
                'to_status' => 'pending_verification',
            ]);

            $challenge = $otpService->send(
                $bloodRequest->requester_phone,
                OtpService::PURPOSE_BLOOD_REQUEST,
                BloodRequest::class,
                $bloodRequest->id
            );

            return [$bloodRequest, $challenge];
        });

        $request->session()->put('redbag_request_tracking_'.$bloodRequest->request_code, $trackingToken);

        return redirect()->route('otp.show', $challenge)->with('success',
            "আপনার আবেদন গ্রহণ করা হয়েছে। Request ID: {$bloodRequest->request_code}। ফোন যাচাইয়ের পর এই ব্রাউজারেই private tracking link দেওয়া হবে।"
        );
    }
}
