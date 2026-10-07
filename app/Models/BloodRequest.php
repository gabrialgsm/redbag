<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model {
    protected $fillable = [
        'request_code','requester_name','requester_phone','patient_name',
        'blood_group','units','urgency','hospital_name','area','district',
        'needed_at','status','expires_at',
    ];
    protected function casts(): array {
        return [
            'needed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}