<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\BloodRequestMatch;
use App\Models\Donor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonorMatchingService
{
    /**
     * Create a private coordination shortlist. This is not clinical matching
     * and does not notify donors; a human must review the result.
     *
     * @return array{created:int,total:int}
     */
    public function shortlist(BloodRequest $bloodRequest): array
    {
        return DB::transaction(function () use ($bloodRequest): array {
            $lockedRequest = BloodRequest::whereKey($bloodRequest->id)->lockForUpdate()->firstOrFail();

            if (!in_array($lockedRequest->status, ['verified', 'matching'], true)
                || !$lockedRequest->requester_phone_verified_at
                || ($lockedRequest->expires_at && $lockedRequest->expires_at->isPast())) {
                throw ValidationException::withMessages([
                    'request' => 'শুধু ফোন-যাচাইকৃত, volunteer-reviewed এবং মেয়াদ থাকা আবেদনেই donor shortlist তৈরি করা যাবে।',
                ]);
            }

            $district = mb_strtolower(trim((string) $lockedRequest->district));
            $area = mb_strtolower(trim((string) $lockedRequest->area));

            $donors = Donor::query()
                ->where('blood_group', $lockedRequest->blood_group)
                ->where('availability', 'available')
                ->whereNotNull('phone_verified_at')
                ->whereNotNull('consent_at')
                ->whereRaw('LOWER(TRIM(district)) = ?', [$district])
                ->when($area !== '', fn ($query) => $query->orderByRaw('CASE WHEN LOWER(TRIM(area)) = ? THEN 0 ELSE 1 END', [$area]))
                ->orderBy('id')
                ->get(['id']);

            $created = 0;

            foreach ($donors as $donor) {
                $match = BloodRequestMatch::firstOrCreate([
                    'blood_request_id' => $lockedRequest->id,
                    'donor_id' => $donor->id,
                ], [
                    'response' => 'pending',
                ]);

                if ($match->wasRecentlyCreated) {
                    $created++;
                }
            }

            $total = BloodRequestMatch::where('blood_request_id', $lockedRequest->id)->count();
            $fromStatus = $lockedRequest->status;

            if ($total > 0 && $lockedRequest->status === 'verified') {
                $lockedRequest->update(['status' => 'matching']);
            }

            BloodRequestEvent::create([
                'blood_request_id' => $lockedRequest->id,
                'actor_type' => 'volunteer',
                'actor_identifier' => session('volunteer_email'),
                'event' => $total > 0 ? 'donors_shortlisted' : 'no_eligible_donors_found',
                'from_status' => $fromStatus,
                'to_status' => $lockedRequest->fresh()->status,
                'reason' => 'Private coordination shortlist only; no SMS or push notification was sent.',
                'metadata' => ['created' => $created, 'total' => $total],
            ]);

            return ['created' => $created, 'total' => $total];
        });
    }
}
