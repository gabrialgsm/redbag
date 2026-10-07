<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonorController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','min:2','max:120'],
            'phone' => ['required','string','regex:/^01[3-9][0-9]{8}$/','unique:donors,phone'],
            'blood_group' => ['required','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'district' => ['required','string','max:80'],
            'area' => ['nullable','string','max:120'],
            'consent' => ['accepted'],
        ], [
            'phone.regex' => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন।',
            'phone.unique' => 'এই নম্বরটি ইতিমধ্যে RED BAG-এ নিবন্ধিত।',
            'consent.accepted' => 'রক্তদাতা হিসেবে যুক্ত হতে সম্মতি দিতে হবে।',
        ]);

        $donor = Donor::create([
            ...$data,
            'donor_code' => 'RED-'.strtoupper(Str::random(8)),
            'availability' => 'available',
            'consent_at' => now(),
        ]);

        return redirect()->route('donor.register')->with('success',
            "ধন্যবাদ {$donor->name}! আপনার RED BAG Donor ID {$donor->donor_code}। পরবর্তী ধাপে মোবাইল OTP verification সম্পন্ন করতে হবে।"
        );
    }
}