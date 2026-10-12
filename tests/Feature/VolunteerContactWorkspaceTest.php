<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\BloodRequestMatch;
use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VolunteerContactWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function request(): BloodRequest
    {
        return BloodRequest::create([
            'request_code' => 'RB-CONTACT01',
            'tracking_token_hash' => hash('sha256', str_repeat('a', 64)),
            'requester_name' => 'Private Requester',
            'requester_phone' => '01712345678',
            'patient_name' => 'Private Patient',
            'blood_group' => 'B+',
            'units' => 1,
            'urgency' => 'urgent',
            'hospital_name' => 'Test Hospital',
            'district' => 'Dhaka',
            'status' => 'matched',
            'requester_phone_verified_at' => now(),
            'expires_at' => now()->addHours(6),
        ]);
    }

    private function donor(): Donor
    {
        return Donor::create([
            'donor_code' => 'RED-CONSENT01',
            'name' => 'Consented Donor',
            'phone' => '01787654321',
            'blood_group' => 'B+',
            'district' => 'Dhaka',
            'availability' => 'available',
            'consent_at' => now(),
            'phone_verified_at' => now(),
        ]);
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

    public function test_contact_workspace_shows_only_accepted_donors_who_consented_and_logs_access(): void
    {
        $request = $this->request();
        $donor = $this->donor();
        $match = BloodRequestMatch::create([
            'blood_request_id' => $request->id,
            'donor_id' => $donor->id,
            'response' => 'accepted',
            'responded_at' => now(),
            'contact_consent_at' => now(),
        ]);

        $this->authenticateVolunteer();

        $this->get(route('volunteer.requests.contacts', $request))
            ->assertOk()
            ->assertSee('01712345678', false)
            ->assertSee('01787654321', false)
            ->assertDontSee('Private Patient', false);

        $this->assertDatabaseHas('blood_request_events', [
            'blood_request_id' => $request->id,
            'event' => 'volunteer_contact_workspace_viewed',
            'actor_identifier' => 'volunteer@example.org',
        ]);
    }

    public function test_contact_log_records_outcome_without_phone_numbers(): void
    {
        $request = $this->request();
        $donor = $this->donor();
        $match = BloodRequestMatch::create([
            'blood_request_id' => $request->id,
            'donor_id' => $donor->id,
            'response' => 'accepted',
            'responded_at' => now(),
            'contact_consent_at' => now(),
        ]);

        $this->authenticateVolunteer();

        $this->post(route('volunteer.requests.contacts.log', $request), [
            'target' => 'donor',
            'outcome' => 'connected',
            'match_id' => $match->id,
        ])->assertRedirect(route('volunteer.requests.contacts', $request));

        $event = BloodRequestEvent::where('blood_request_id', $request->id)
            ->where('event', 'volunteer_contact_logged')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('volunteer@example.org', $event->actor_identifier);
        $this->assertSame('connected', $event->metadata['outcome']);
        $this->assertSame('RED-CONSENT01', $event->metadata['donor_code']);
        $this->assertStringNotContainsString('01712345678', json_encode($event->metadata));
        $this->assertStringNotContainsString('01787654321', json_encode($event->metadata));
    }

    public function test_contact_workspace_is_not_public(): void
    {
        $request = $this->request();

        $this->get(route('volunteer.requests.contacts', $request))
            ->assertRedirect('/volunteer/login');
    }
}
