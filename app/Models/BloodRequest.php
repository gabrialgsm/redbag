<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodRequest extends Model
{
    protected $fillable = [
        'request_code','requester_name','requester_phone','patient_name',
        'blood_group','units','urgency','hospital_name','area','district',
        'needed_at','status','requester_phone_verified_at','expires_at',
    ];

    protected function casts(): array
    {
        return [
            'needed_at' => 'datetime',
            'requester_phone_verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(BloodRequestEvent::class);
    }
}
