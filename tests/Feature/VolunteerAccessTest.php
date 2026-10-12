<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VolunteerAccessTest extends TestCase
{
    public function test_volunteer_login_page_renders_when_portal_is_not_configured(): void
    {
        $this->withoutVite();

        config([
            'redbag.volunteer_email' => null,
            'redbag.volunteer_password_hash' => null,
        ]);

        $this->get('/volunteer/login')
            ->assertOk()
            ->assertSee('Volunteer portal এখন বন্ধ আছে', false);
    }

    public function test_review_queue_redirects_when_volunteer_credentials_are_not_configured(): void
    {
        config([
            'redbag.volunteer_email' => null,
            'redbag.volunteer_password_hash' => null,
        ]);

        $this->get('/volunteer/requests')
            ->assertRedirect('/volunteer/login');
    }

    public function test_login_is_rejected_when_portal_is_not_configured(): void
    {
        config([
            'redbag.volunteer_email' => null,
            'redbag.volunteer_password_hash' => null,
        ]);

        $this->post('/volunteer/login', [
            'email' => 'volunteer@example.org',
            'password' => 'not-a-real-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_configured_volunteer_can_login_and_session_is_regenerated(): void
    {
        config([
            'redbag.volunteer_email' => 'volunteer@example.org',
            'redbag.volunteer_password_hash' => Hash::make('correct-test-password'),
        ]);

        $response = $this->post('/volunteer/login', [
            'email' => 'volunteer@example.org',
            'password' => 'correct-test-password',
        ]);

        $response->assertRedirect('/volunteer/requests');
        $response->assertSessionHas('volunteer_authenticated', true);
        $response->assertSessionHas('volunteer_email', 'volunteer@example.org');
    }
}
