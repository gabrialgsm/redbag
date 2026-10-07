<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonorDashboardController extends Controller
{
    private function donor(Request $request): ?Donor
    {
        $id = $request->session()->get('donor_id');

        return $id ? Donor::find($id) : null;
    }

    public function show(Request $request): View|RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor) {
            return redirect()->route('donor.register')->with('error', 'Donor dashboard দেখতে আগে login/registration করতে হবে।');
        }

        return view('donor-dashboard', compact('donor'));
    }

    public function availability(Request $request): RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor) {
            return redirect()->route('donor.register')->with('error', 'আপনার donor session পাওয়া যায়নি।');
        }

        $data = $request->validate([
            'availability' => ['required', 'in:available,maybe,unavailable'],
        ]);

        $donor->update(['availability' => $data['availability']]);

        return redirect()->route('donor.dashboard')->with('success', 'আপনার availability সফলভাবে আপডেট হয়েছে।');
    }

    public function profile(Request $request): RedirectResponse
    {
        $donor = $this->donor($request);

        if (!$donor) {
            return redirect()->route('donor.register')->with('error', 'আপনার donor session পাওয়া যায়নি।');
        }

        $data = $request->validate([
            'district' => ['required', 'string', 'max:80'],
            'area' => ['nullable', 'string', 'max:120'],
        ]);

        $donor->update($data);

        return redirect()->route('donor.dashboard')->with('success', 'আপনার location profile আপডেট হয়েছে।');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('donor_id');
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'আপনাকে RED BAG donor dashboard থেকে logout করা হয়েছে।');
    }
}
