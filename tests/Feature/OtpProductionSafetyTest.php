<?php

namespace Tests\Feature;

use App\Services\OtpService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OtpProductionSafetyTest extends TestCase
{
    public function test_log_otp_driver_is_disabled_in_production(): void
    {
        config(['otp.driver' => 'log']);
        $this->app->detectEnvironment(fn () => 'production');

        $service = app(OtpService::class);

        $this->assertFalse($service->deliveryConfigured());
        $this->expectException(ValidationException::class);

        // Must fail before querying or creating any OTP challenge.
        $service->send('01712345678', 'blood_request');
    }

    public function test_log_otp_driver_is_available_in_testing_only(): void
    {
        config(['otp.driver' => 'log']);
        $this->app->detectEnvironment(fn () => 'testing');

        $this->assertTrue(app(OtpService::class)->deliveryConfigured());
    }

    public function test_unimplemented_sms_driver_is_not_misreported_as_configured(): void
    {
        config(['otp.driver' => 'sms']);
        $this->app->detectEnvironment(fn () => 'testing');

        $this->assertFalse(app(OtpService::class)->deliveryConfigured());
    }
}
