<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'donor_code','name','phone','email','blood_group','area','district',
        'latitude','longitude','availability','last_donation_date',
        'donation_count','verified_at','phone_verified_at','consent_at',
    ];

    protected function casts(): array
    {
        return [
            'last_donation_date' => 'date',
            'verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'consent_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }
}
