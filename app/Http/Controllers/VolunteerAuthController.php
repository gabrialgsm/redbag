<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class VolunteerAuthController extends Controller
{
    public function show(): View
    {
        return view('volunteer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'max:1024'],
        ]);

        $configuredEmail = config('redbag.volunteer_email');
        $configuredHash = config('redbag.volunteer_password_hash');

        if (!$configuredEmail || !$configuredHash) {
            return back()->withErrors([
                'email' => 'Volunteer portal এখনো server configuration দিয়ে চালু করা হয়নি।',
            ])->onlyInput('email');
        }

        if (!hash_equals(strtolower((string) $configuredEmail), strtolower($data['email']))
            || !Hash::check($data['password'], $configuredHash)) {
            return back()->withErrors([
                'email' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'volunteer_authenticated' => true,
            'volunteer_email' => strtolower((string) $configuredEmail),
        ]);

        return redirect()->route('volunteer.requests')
            ->with('success', 'Volunteer portal-এ স্বাগতম।');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['volunteer_authenticated', 'volunteer_email']);
        $request->session()->regenerateToken();

        return redirect()->route('volunteer.login')
            ->with('success', 'আপনি volunteer portal থেকে logout করেছেন।');
    }
}
