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
        $response->assertSee('রক্ত লাগবে? অনুরোধ করুন', false);
        $response->assertSee('ডোনার খুঁজুন', false);
        $response->assertSee('কারও request-এ সাড়া দিন', false);
        $response->assertSee('ডোনার হোন', false);
        $response->assertSee(route('blood.request'), false);
        $response->assertSee(route('donor.find'), false);
        $response->assertSee(route('donor.requests'), false);
        $response->assertSee(route('donor.register'), false);
        $response->assertSee('এই মুহূর্তে তালিকায় কোনো যাচাইকৃত জরুরি অনুরোধ নেই', false);
        $response->assertDontSee('৮,৬০০+', false);
        $response->assertDontSee('২১০+', false);
        $response->assertDontSee('Square Hospital', false);
        $response->assertDontSee('সাম্প্রতিক রক্তের আবেদন', false);
    }

    public function test_homepage_blood_search_filters_verified_requests_and_orders_by_urgency(): void
    {
        $this->withoutVite();

        foreach ([
            ['RB-SEARCH01', 'O+', 'emergency', 'Dhaka', 'Urgent Search Hospital'],
            ['RB-SEARCH02', 'O+', 'normal', 'Dhaka', 'Routine Search Hospital'],
            ['RB-SEARCH03', 'A+', 'urgent', 'Chattogram', 'Other Group Hospital'],
        ] as [$code, $group, $urgency, $district, $hospital]) {
            $request = BloodRequest::create([
                'request_code' => $code,
                'tracking_token_hash' => hash('sha256', $code),
                'requester_name' => 'Private Person',
                'requester_phone' => '01712345678',
                'blood_group' => $group,
                'units' => 1,
                'urgency' => $urgency,
                'hospital_name' => $hospital,
                'district' => $district,
                'status' => 'verified',
                'expires_at' => now()->addHours(8),
            ]);
            $request->forceFill(['requester_phone_verified_at' => now()])->save();
        }

        $response = $this->get('/?blood_group=O%2B&district=Dhaka');

        $response->assertOk();
        $response->assertSee('Urgent Search Hospital', false);
        $response->assertSee('Routine Search Hospital', false);
        $response->assertDontSee('Other Group Hospital', false);
        $this->assertLessThan(
            strpos($response->getContent(), 'Routine Search Hospital'),
            strpos($response->getContent(), 'Urgent Search Hospital')
        );
    }

    public function test_donor_finding_page_explains_safe_matching_process_without_claiming_guaranteed_donation(): void
    {
        $this->withoutVite();

        $response = $this->get('/find-donor');

        $response->assertOk();
        $response->assertSee('রক্তদাতা খুঁজুন', false);
        $response->assertSee('রক্তের অনুরোধ করুন', false);
        $response->assertSee('Donor-এর ব্যক্তিগত ফোন নম্বর public directory-তে দেখানো হয় না।', false);
        $response->assertSee('রক্ত পাওয়া নিশ্চিত করে না।', false);
    }

    public function test_public_emergency_list_only_shows_verified_active_requests_and_hides_phone_numbers(): void
    {
        $this->withoutVite();

        $verifiedRequest = BloodRequest::create([
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
            'expires_at' => now()->addHours(5),
        ]);
        $verifiedRequest->forceFill(['requester_phone_verified_at' => now()])->save();

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
