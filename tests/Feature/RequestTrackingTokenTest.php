<?php

namespace Tests\Feature;

use App\Services\RequestTrackingTokenService;
use Tests\TestCase;

class RequestTrackingTokenTest extends TestCase
{
    public function test_tracking_tokens_are_high_entropy_and_only_the_hash_is_storable(): void
    {
        $service = app(RequestTrackingTokenService::class);
        $token = $service->generate();
        $hash = $service->hash($token);

        $this->assertSame(64, strlen($token));
        $this->assertSame(64, strlen($hash));
        $this->assertNotSame($token, $hash);
        $this->assertTrue($service->matches($token, $hash));
        $this->assertFalse($service->matches(str_repeat('a', 64), $hash));
        $this->assertFalse($service->matches('short-token', $hash));
    }

    public function test_tracking_route_does_not_reveal_request_without_token(): void
    {
        $this->withoutVite();

        $this->get('/blood-request/status/RB-DOESNOTEXIST')
            ->assertNotFound();
    }
}
