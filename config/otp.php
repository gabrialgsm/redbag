<?php

return [
    'driver' => env('OTP_DRIVER', 'log'),
    'length' => 6,
    'expiry_minutes' => 5,
    'max_attempts' => 5,
    'resend_wait_seconds' => 120,
];
