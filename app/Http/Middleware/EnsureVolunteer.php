<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVolunteer
{
    public function handle(Request $request, Closure $next): Response
    {
        $email = config('redbag.volunteer_email');
        $passwordHash = config('redbag.volunteer_password_hash');

        if (!$email || !$passwordHash) {
            $request->session()->forget(['volunteer_authenticated', 'volunteer_email']);

            return redirect()->route('volunteer.login')
                ->with('error', 'Volunteer portal এখনো server configuration দিয়ে চালু করা হয়নি।');
        }

        $sessionEmail = strtolower((string) $request->session()->get('volunteer_email', ''));

        if (!(bool) $request->session()->get('volunteer_authenticated', false)
            || !hash_equals(strtolower((string) $email), $sessionEmail)) {
            return redirect()->route('volunteer.login')
                ->with('error', 'এই পেজ দেখতে volunteer login প্রয়োজন।');
        }

        return $next($request);
    }
}
