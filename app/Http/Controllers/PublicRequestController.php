<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicRequestController extends Controller
{
    private function activePublicRequests()
    {
        return BloodRequest::query()
            ->whereIn('status', ['verified', 'matching', 'matched'])
            ->whereNotNull('requester_phone_verified_at')
            ->whereIn('urgency', ['normal', 'urgent', 'emergency'])
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    private function applyPublicFilters($query, Request $request)
    {
        $bloodGroup = $request->query('blood_group');
        if (in_array($bloodGroup, ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], true)) {
            $query->where('blood_group', $bloodGroup);
        }

        $district = trim((string) $request->query('district', ''));
        if ($district !== '' && mb_strlen($district) <= 80) {
            $query->where('district', 'like', '%'.$district.'%');
        }

        return $query;
    }

    private function urgencyOrder($query)
    {
        return $query
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderByRaw('CASE WHEN needed_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('needed_at')
            ->orderByDesc('created_at');
    }

    public function home(Request $request): View
    {
        $emergencyRequests = $this->urgencyOrder(
            $this->applyPublicFilters($this->activePublicRequests(), $request)
        )->limit(3)->get(['request_code', 'blood_group', 'units', 'urgency', 'hospital_name', 'district', 'area', 'needed_at', 'created_at']);

        return view('home', [
            'emergencyRequests' => $emergencyRequests,
            'selectedBloodGroup' => $request->query('blood_group', ''),
            'selectedDistrict' => $request->query('district', ''),
            'hasSearch' => $request->filled('blood_group') || $request->filled('district'),
        ]);
    }

    public function emergencies(Request $request): View
    {
        $emergencyRequests = $this->urgencyOrder(
            $this->applyPublicFilters($this->activePublicRequests(), $request)
        )->paginate(12, ['request_code', 'blood_group', 'units', 'urgency', 'hospital_name', 'district', 'area', 'needed_at', 'created_at'])
         ->withQueryString();

        return view('emergency-requests', [
            'emergencyRequests' => $emergencyRequests,
            'selectedBloodGroup' => $request->query('blood_group', ''),
            'selectedDistrict' => $request->query('district', ''),
        ]);
    }
}
