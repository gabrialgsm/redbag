<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Services\RequestTrackingTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RequestTrackingController extends Controller
{
    private function trackedRequest(Request $request, string $requestCode, RequestTrackingTokenService $tokens): BloodRequest
    {
        $token = $request->input('token', $request->query('token'));

        if (!is_string($token) || !preg_match('/^[A-Za-z0-9]{64}$/', $token)) {
            abort(404);
        }

        $bloodRequest = BloodRequest::where('request_code', $requestCode)->first();

        if (!$bloodRequest || !$tokens->matches($token, $bloodRequest->tracking_token_hash)) {
            abort(404);
        }

        return $bloodRequest;
    }

    public function show(Request $request, string $requestCode, RequestTrackingTokenService $tokens): View
    {
        $bloodRequest = $this->trackedRequest($request, $requestCode, $tokens);
        $events = $bloodRequest->events()->orderBy('created_at')->get(['event', 'created_at']);

        return view('blood-request-status', [
            'bloodRequest' => $bloodRequest,
            'events' => $events,
            'trackingToken' => $request->query('token'),
            'isExpired' => $bloodRequest->expires_at?->isPast()
                && !in_array($bloodRequest->status, ['completed', 'cancelled'], true),
        ]);
    }

    public function cancel(Request $request, string $requestCode, RequestTrackingTokenService $tokens): RedirectResponse
    {
        $bloodRequest = $this->trackedRequest($request, $requestCode, $tokens);

        DB::transaction(function () use ($bloodRequest): void {
            $locked = BloodRequest::whereKey($bloodRequest->id)->lockForUpdate()->firstOrFail();

            if (!in_array($locked->status, ['pending_verification', 'needs_info', 'verified', 'matching', 'matched'], true)) {
                abort(409, 'এই আবেদনটি এখন আর বাতিল করা যাবে না।');
            }

            $fromStatus = $locked->status;
            $locked->update(['status' => 'cancelled']);

            BloodRequestEvent::create([
                'blood_request_id' => $locked->id,
                'actor_type' => 'requester',
                'event' => 'requester_cancelled',
                'from_status' => $fromStatus,
                'to_status' => 'cancelled',
            ]);
        });

        return redirect()->route('blood.request.track', [
            'requestCode' => $bloodRequest->request_code,
            'token' => $request->input('token'),
        ])->with('success', 'আপনার আবেদন বাতিল করা হয়েছে।');
    }
}
