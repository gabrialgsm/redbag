<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\BloodRequestEvent;
use App\Models\BloodRequestMatch;
use App\Models\Donor;
use App\Services\DonorMatchingService;
use App\Services\RequestTrackingTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DonorMatchingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function request(array $overrides = []): BloodRequest
    {
        return BloodRequest::create(array_merge([
            'request_code' => 'RB-'.strtoupper(bin2hex(random_bytes(4))),
            'tracking_token_hash' => app(RequestTrackingTokenService::class)->hash(str_repeat('a', 64)),
            'requester_name' => 'Private Requester Name',
            'requester_phone' => '01712345678',
            'patient_name' => 'Private Patient Name',
            'blood_group' => 'B+',
            'units' => 2,
            'urgency' => 'emergency',
            'hospital_name' => 'Test Hospital',
            'district' => 'Dhaka',
            'area' => 'Gulshan',
            'status' => 'verified',
            'requester_phone_verified_at' => now(),
            'expires_at' => now()->addHours(12),
        ], $overrides));
    }

    private function donor(array $overrides = []): Donor
    {
        static $sequence = 0;
        $sequence++;

        return Donor::create(array_merge([
            'donor_code' => 'RED-TEST'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'name' => 'Private Donor Name '.$sequence,
            'phone' => '017'.str_pad((string) (10000000 + $sequence), 8, '0', STR_PAD_LEFT),
            'blood_group' => 'B+',
            'district' => 'Dhaka',
            'area' => 'Gulshan',
            'availability' => 'available',
            'consent_at' => now(),
            'phone_verified_at' => now(),
        ], $overrides));
    }

    public function test_shortlist_only_includes_verified_available_consented_donors_in_the_same_group_and_district(): void
    {
        $request = $this->request();
        $eligible = $this->donor();
        $this->donor(['phone_verified_at' => null]);
        $this->donor(['availability' => 'unavailable']);
        $this->donor(['blood_group' => 'O+']);
        $this->donor(['district' => 'Chattogram']);

        $result = app(DonorMatchingService::class)->shortlist($request);

        $this->assertSame(['created' => 1, 'total' => 1], $result);
        $this->assertSame('matching', $request->fresh()->status);
        $this->assertDatabaseHas('blood_request_matches', [
            'blood_request_id' => $request->id,
            'donor_id' => $eligible->id,
            'response' => 'pending',
        ]);
        $this->assertSame(1, $request->events()->where('event', 'donors_shortlisted')->count());
    }

    public function test_unverified_request_cannot_be_shortlisted(): void
    {
        $request = $this->request([
            'status' => 'pending_verification',
            'requester_phone_verified_at' => null,
        ]);

        try {
            app(DonorMatchingService::class)->shortlist($request);
            $this->fail('Expected an unverified request to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('request', $exception->errors());
        }

        $this->assertDatabaseCount('blood_request_matches', 0);
    }

    public function test_accepted_donor_response_requires_contact_consent_and_public_tracking_hides_contact_data(): void
    {
        $request = $this->request();
        $donor = $this->donor();
        $match = BloodRequestMatch::create([
            'blood_request_id' => $request->id,
            'donor_id' => $donor->id,
            'response' => 'pending',
        ]);

        $this->withSession(['donor_id' => $donor->id])
            ->post(route('donor.requests.respond', $match), [
                'response' => 'accepted',
                'contact_consent' => '1',
            ])
            ->assertRedirect(route('donor.requests'));

        $this->assertDatabaseHas('blood_request_matches', [
            'id' => $match->id,
            'response' => 'accepted',
        ]);
        $this->assertNotNull($match->fresh()->contact_consent_at);
        $this->assertSame('matched', $request->fresh()->status);
        $this->assertDatabaseHas('blood_request_events', [
            'blood_request_id' => $request->id,
            'event' => 'donor_response_accepted',
        ]);

        $token = str_repeat('a', 64);
        $this->withoutVite();
        $response = $this->get(route('blood.request.track', [
            'requestCode' => $request->request_code,
            'token' => $token,
        ]));

        $response->assertOk()
            ->assertSee('Test Hospital', false)
            ->assertDontSee('01712345678', false)
            ->assertDontSee($donor->phone, false)
            ->assertDontSee('Private Requester Name', false)
            ->assertDontSee('Private Patient Name', false)
            ->assertDontSee($donor->name, false);
    }

    public function test_donor_cannot_respond_to_another_donors_match(): void
    {
        $request = $this->request(['status' => 'matching']);
        $owner = $this->donor();
        $other = $this->donor();
        $match = BloodRequestMatch::create([
            'blood_request_id' => $request->id,
            'donor_id' => $owner->id,
            'response' => 'pending',
        ]);

        $this->withSession(['donor_id' => $other->id])
            ->post(route('donor.requests.respond', $match), [
                'response' => 'declined',
            ])
            ->assertNotFound();

        $this->assertSame('pending', $match->fresh()->response);
    }
}
