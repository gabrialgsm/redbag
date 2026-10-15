<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTruthfulnessTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_clear_service_actions_without_fake_metrics_or_requests(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('আমার রক্ত প্রয়োজন', false);
        $response->assertSee('জরুরি অনুরোধ দেখুন', false);
        $response->assertSee('আমি রক্ত দিতে চাই', false);
        $response->assertSee('এই মুহূর্তে তালিকায় কোনো যাচাইকৃত জরুরি অনুরোধ নেই', false);
        $response->assertDontSee('৮,৬০০+', false);
        $response->assertDontSee('২১০+', false);
        $response->assertDontSee('Square Hospital', false);
        $response->assertDontSee('সাম্প্রতিক রক্তের আবেদন', false);
    }

    public function test_public_emergency_list_only_shows_verified_active_requests_and_hides_phone_numbers(): void
    {
        $this->withoutVite();

        BloodRequest::create([
            'request_code' => 'RB-PUBLIC001',
            'tracking_token_hash' => hash('sha256', str_repeat('a', 64)),
            'requester_name' => 'Private Requester',
            'requester_phone' => '01712345678',
            'blood_group' => 'O+',
            'units' => 2,
            'urgency' => 'emergency',
            'hospital_name' => 'Verified General Hospital',
            'district' => 'Dhaka',
            'area' => 'Dhanmondi',
            'status' => 'verified',
            'requester_phone_verified_at' => now(),
            'expires_at' => now()->addHours(5),
        ]);

        BloodRequest::create([
            'request_code' => 'RB-PENDING01',
            'tracking_token_hash' => hash('sha256', str_repeat('b', 64)),
            'requester_name' => 'Unverified Requester',
            'requester_phone' => '01812345678',
            'blood_group' => 'A+',
            'units' => 1,
            'urgency' => 'emergency',
            'hospital_name' => 'Pending Hospital',
            'district' => 'Dhaka',
            'status' => 'pending_verification',
            'expires_at' => now()->addHours(5),
        ]);

        $response = $this->get('/emergency-requests');

        $response->assertOk();
        $response->assertSee('Verified General Hospital', false);
        $response->assertSee('RB-PUBLIC001', false);
        $response->assertDontSee('Pending Hospital', false);
        $response->assertDontSee('01712345678', false);
        $response->assertDontSee('Private Requester', false);
    }

    public function test_safety_page_explains_phone_verification_is_not_request_legitimacy(): void
    {
        $this->withoutVite();

        $response = $this->get('/safety');

        $response->assertOk();
        $response->assertSee('ফোন যাচাই ও আবেদন যাচাই আলাদা', false);
        $response->assertSee('স্বয়ংক্রিয় donor matching', false);
    }
}
