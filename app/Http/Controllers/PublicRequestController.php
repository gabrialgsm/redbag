<?php

namespace App\\Http\\Controllers;

use App\\Models\\BloodRequest;
use Illuminate\\View\\View;

class PublicRequestController extends Controller
{
    private function activeEmergencyRequests()
    {
        return BloodRequest::query()
            ->whereIn('status', ['verified', 'matching', 'matched'])
            ->whereNotNull('requester_phone_verified_at')
            ->whereIn('urgency', ['urgent', 'emergency'])
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function home(): View
    {
        $emergencyRequests = $this->activeEmergencyRequests()
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderBy('needed_at')
            ->limit(3)
            ->get(['request_code', 'blood_group', 'units', 'urgency', 'hospital_name', 'district', 'area', 'needed_at', 'created_at']);

        return view('home', compact('emergencyRequests'));
    }

    public function emergencies(): View
    {
        $emergencyRequests = $this->activeEmergencyRequests()
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderBy('needed_at')
            ->paginate(12, ['request_code', 'blood_group', 'units', 'urgency', 'hospital_name', 'district', 'area', 'needed_at', 'created_at']);

        return view('emergency-requests', compact('emergencyRequests'));
    }
}
