<?php

namespace App\Services;

use Illuminate\Support\Str;

class RequestTrackingTokenService
{
    public function generate(): string
    {
        return Str::random(64);
    }

    public function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    public function matches(string $token, ?string $storedHash): bool
    {
        return is_string($storedHash)
            && strlen($token) === 64
            && hash_equals($storedHash, $this->hash($token));
    }
}
