@extends('layouts.app')

@section('title', 'Donor Dashboard — RED BAG')

@section('content')
<section class="page-section donor-dashboard-page">
    <div class="container">
        <div class="dashboard-head">
            <div>
                <span class="eyebrow">RED BAG • DONOR DASHBOARD</span>
                <h1>স্বাগতম, {{ $donor->name }} ❤️</h1>
                <p class="muted">আপনি যখন available থাকবেন, RED BAG প্রয়োজন অনুযায়ী আপনার কাছে সাহায্যের alert পাঠাতে পারবে।</p>
            </div>
            <form method="POST" action="{{ route('donor.logout') }}">
                @csrf
                <button class="btn btn-soft" type="submit">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <div class="donor-status-card">
            <div class="status-main">
                <div class="blood-badge large">{{ $donor->blood_group }}</div>
                <div>
                    <span class="muted">আপনার RED BAG ID</span>
                    <strong>{{ $donor->donor_code }}</strong>
                    <small>{{ $donor->phone }}</small>
                </div>
            </div>
            <div class="verification-pill">
                <span class="status-dot"></span>
                Mobile Verified
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-heading"><h2>আমার Availability</h2><span>এখন</span></div>
                <p class="muted">আপনি এখন রক্ত দিতে পারবেন কি না সেটি আপডেট করুন।</p>
                <div class="availability-grid">
                    <form method="POST" action="{{ route('donor.availability') }}">
                        @csrf
                        <input type="hidden" name="availability" value="available">
                        <button class="availability-option {{ $donor->availability === 'available' ? 'selected available' : '' }}" type="submit">
                            <b>আমি সাহায্য করতে পারি</b><small>Available</small>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('donor.availability') }}">
                        @csrf
                        <input type="hidden" name="availability" value="maybe">
                        <button class="availability-option {{ $donor->availability === 'maybe' ? 'selected maybe' : '' }}" type="submit">
                            <b>সম্ভব হলে সাহায্য করব</b><small>Maybe</small>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('donor.availability') }}">
                        @csrf
                        <input type="hidden" name="availability" value="unavailable">
                        <button class="availability-option {{ $donor->availability === 'unavailable' ? 'selected unavailable' : '' }}" type="submit">
                            <b>এখন পারব না</b><small>Unavailable</small>
                        </button>
                    </form>
                </div>
            </div>

            <div class="dashboard-card impact-card">
                <div class="card-heading"><h2>আমার Impact</h2></div>
                <div class="impact-number">{{ $donor->donation_count }}</div>
                <p>টি donation recorded</p>
                <div class="impact-note">একটি donation-ও কারও জীবনের জন্য অনেক বড় সাহায্য।</div>
            </div>

            <div class="dashboard-card">
                <div class="card-heading"><h2>আমার Profile</h2><span>Private</span></div>
                <form method="POST" action="{{ route('donor.profile') }}" class="dashboard-form">
                    @csrf
                    <label>District
                        <input name="district" value="{{ old('district', $donor->district) }}" required>
                    </label>
                    <label>Area
                        <input name="area" value="{{ old('area', $donor->area) }}" placeholder="এলাকা / থানা">
                    </label>
                    <button class="btn btn-primary" type="submit">Profile Update</button>
                </form>
            </div>

            <div class="dashboard-card">
                <div class="card-heading"><h2>Donation History</h2><span>Coming next</span></div>
                <div class="empty-state">
                    <div>🩸</div>
                    <strong>আপনার donation history এখানে দেখা যাবে</strong>
                    <p>Donation completion flow তৈরি হলে প্রতিটি donation-এর তারিখ ও impact এখানে যুক্ত হবে।</p>
                </div>
            </div>
        </div>

        <div class="privacy-banner">
            <strong>🔒 আপনার তথ্য নিরাপদ</strong>
            <span>আপনার mobile number বা ব্যক্তিগত তথ্য public donor search-এ দেখানো হবে না। প্রয়োজন হলে verification ও consent-এর পরে connection তৈরি হবে।</span>
        </div>
    </div>
</section>
@endsection
