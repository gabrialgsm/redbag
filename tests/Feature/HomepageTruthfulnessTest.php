<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomepageTruthfulnessTest extends TestCase
{
    public function test_homepage_discloses_that_automatic_matching_is_not_live(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('স্বয়ংক্রিয় donor matching', false);
        $response->assertDontSee('৮,৬০০+', false);
        $response->assertDontSee('২১০+', false);
        $response->assertDontSee('Square Hospital', false);
        $response->assertDontSee('সাম্প্রতিক রক্তের আবেদন', false);
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
