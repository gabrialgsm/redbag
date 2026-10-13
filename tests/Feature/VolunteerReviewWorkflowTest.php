<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VolunteerReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function request(array $overrides = []): BloodRequest
    {
        static $sequence = 0;
        $sequence++;

        return BloodRequest::create(array_merge([
            'request_code' => 'RB-REVIEW'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            'tracking_token_hash' => hash('sha256', str_repeat('a', 64)),
            'requester_name' => 'Test Requester',
            'requester_phone' => '01712345678',
            'blood_group' => 'B+',
            'units' => 1,
            'urgency' => 'urgent',
            'hospital_name' => 'Test Hospital',
            'district' => 'Dhaka',
            'status' => 'pending_verification',
            'expires_at' => now()->addHours(12),
        ], $overrides));
    }

    private function authenticateVolunteer(): void
    {
        config([
            'redbag.volunteer_email' => 'volunteer@example.org',
            'redbag.volunteer_password_hash' => Hash::make('test-password'),
        ]);

        $this->withSession([
            'volunteer_authenticated' => true,
            'volunteer_email' => 'volunteer@example.org',
        ]);
    }

    public function test_unverified_request_cannot_be_approved(): void
    {
        $request = $this->request();
        $this->authenticateVolunteer();

        $this->post(route('volunteer.requests.review', $request), [
            'action' => 'approve',
        ])->assertSessionHasErrors('action');

        $this->assertSame('pending_verification', $request->fresh()->status);
        $this->assertSame(0, $request->events()->where('event', 'request_approved')->count());
    }

    public function test_phone_verified_request_can_be_approved_and_audited(): void
    {
        $request = $this->request(['requester_phone_verified_at' => now()]);
        $this->authenticateVolunteer();

        $this->post(route('volunteer.requests.review', $request), [
            'action' => 'approve',
        ])->assertRedirect(route('volunteer.requests'));

        $this->assertSame('verified', $request->fresh()->status);
        $this->assertDatabaseHas('blood_request_events', [
            'blood_request_id' => $request->id,
            'actor_type' => 'volunteer',
            'actor_identifier' => 'volunteer@example.org',
            'event' => 'request_approved',
            'from_status' => 'pending_verification',
            'to_status' => 'verified',
        ]);
    }

    public function test_rejection_requires_reason_and_records_state_transition(): void
    {
        $request = $this->request();
        $this->authenticateVolunteer();

        $this->post(route('volunteer.requests.review', $request), [
            'action' => 'reject',
            'reason' => 'no',
        ])->assertSessionHasErrors('reason');

        $this->assertSame('pending_verification', $request->fresh()->status);

        $this->post(route('volunteer.requests.review', $request), [
            'action' => 'reject',
            'reason' => 'Request could not be verified',
        ])->assertRedirect(route('volunteer.requests'));

        $this->assertSame('cancelled', $request->fresh()->status);
        $this->assertDatabaseHas('blood_request_events', [
            'blood_request_id' => $request->id,
            'event' => 'request_rejected',
            'from_status' => 'pending_verification',
            'to_status' => 'cancelled',
            'reason' => 'Request could not be verified',
        ]);
    }

    public function test_needs_info_records_reason_without_approving_request(): void
    {
        $request = $this->request();
        $this->authenticateVolunteer();

        $this->post(route('volunteer.requests.review', $request), [
            'action' => 'needs_info',
            'reason' => 'Please confirm the hospital details',
        ])->assertRedirect(route('volunteer.requests'));

        $this->assertSame('pending_verification', $request->fresh()->status);
        $this->assertDatabaseHas('blood_request_events', [
            'blood_request_id' => $request->id,
            'event' => 'more_information_requested',
            'reason' => 'Please confirm the hospital details',
        ]);
    }
}
