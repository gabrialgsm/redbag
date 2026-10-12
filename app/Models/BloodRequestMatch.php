<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequestMatch extends Model
{
    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'response',
        'responded_at',
        'contact_consent_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'contact_consent_at' => 'datetime',
        ];
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
