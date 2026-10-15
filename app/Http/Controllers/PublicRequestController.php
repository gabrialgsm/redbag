<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
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
        $availableDonors = Donor::query()
            ->whereNotNull('phone_verified_at')
            ->whereNotNull('consent_at')
            ->where('availability', 'available');

        $activeRequests = $this->activePublicRequests();

        $emergencyRequests = $this->urgencyOrder(
            $this->applyPublicFilters(clone $activeRequests, $request)
        )->limit(6)->get(['id', 'request_code', 'blood_group', 'units', 'urgency', 'hospital_name', 'district', 'area', 'needed_at', 'created_at']);

        $donorsByDistrict = (clone $availableDonors)
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->selectRaw('district, COUNT(*) as total')
            ->groupBy('district')
            ->get()
            ->keyBy(fn ($row) => mb_strtolower(trim($row->district)));

        $requestsByDistrict = (clone $activeRequests)
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->selectRaw('district, COUNT(*) as total')
            ->groupBy('district')
            ->get()
            ->keyBy(fn ($row) => mb_strtolower(trim($row->district)));

        $districtKeys = $donorsByDistrict->keys()->merge($requestsByDistrict->keys())->unique();
        $locationRows = $districtKeys->map(function ($key) use ($donorsByDistrict, $requestsByDistrict) {
            $donorRow = $donorsByDistrict->get($key);
            $requestRow = $requestsByDistrict->get($key);

            return [
                'district' => $donorRow?->district ?? $requestRow?->district,
                'donors' => (int) ($donorRow->total ?? 0),
                'requests' => (int) ($requestRow->total ?? 0),
            ];
        })->sort(function ($a, $b) {
            return ($b['requests'] <=> $a['requests']) ?: ($b['donors'] <=> $a['donors']);
        })->take(6)->values();

        $selectedDistrict = trim((string) $request->query('district', ''));
        $districtDonorCount = $selectedDistrict === '' ? null : (clone $availableDonors)
            ->where('district', 'like', '%'.$selectedDistrict.'%')->count();
        $districtRequestCount = $selectedDistrict === '' ? null : (clone $activeRequests)
            ->where('district', 'like', '%'.$selectedDistrict.'%')->count();

        $districtCount = $districtKeys->count();

        return view('home', [
            'emergencyRequests' => $emergencyRequests,
            'selectedBloodGroup' => $request->query('blood_group', ''),
            'selectedDistrict' => $selectedDistrict,
            'hasSearch' => $request->filled('blood_group') || $request->filled('district'),
            'locationRows' => $locationRows,
            'availableDonorCount' => (clone $availableDonors)->count(),
            'activeRequestCount' => (clone $activeRequests)->count(),
            'districtCount' => $districtCount,
            'districtDonorCount' => $districtDonorCount,
            'districtRequestCount' => $districtRequestCount,
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
