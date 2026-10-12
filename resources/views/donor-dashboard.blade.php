@extends('layouts.app')

@section('title', 'Donor Dashboard — RED BAG')

@section('content')
<section class="page-section donor-dashboard-page">
    <div class="container">
        <div class="dashboard-head">
            <div>
                <span class="eyebrow">RED BAG • DONOR DASHBOARD</span>
                <h1>স্বাগতম, {{ $donor->name }} ❤️</h1>
                <p class="muted">আপনার availability ও private request inbox এখান থেকে পরিচালনা করুন। বর্তমানে SMS/push notification পাঠানো হয় না।</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ route('donor.requests') }}">আমার request inbox</a>
                <form method="POST" action="{{ route('donor.logout') }}">@csrf<button class="btn btn-soft" type="submit">Logout</button></form>
            </div>
        </div>

        @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <div class="donor-status-card">
            <div class="status-main">
                <div class="blood-badge large">{{ $donor->blood_group }}</div>
                <div><span class="muted">আপনার RED BAG ID</span><strong>{{ $donor->donor_code }}</strong><small>{{ substr($donor->phone, 0, 3).'******'.substr($donor->phone, -2) }}</small></div>
            </div>
            <div class="verification-pill"><span class="status-dot"></span>Mobile Verified</div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-heading"><h2>আমার Availability</h2><span>এখন</span></div>
                <p class="muted">আপনি এখন রক্ত দিতে পারবেন কি না সেটি আপডেট করুন।</p>
                <div class="availability-grid">
                    <form method="POST" action="{{ route('donor.availability') }}">@csrf<input type="hidden" name="availability" value="available"><button class="availability-option {{ $donor->availability === 'available' ? 'selected available' : '' }}" type="submit"><b>আমি সাহায্য করতে পারি</b><small>Available</small></button></form>
                    <form method="POST" action="{{ route('donor.availability') }}">@csrf<input type="hidden" name="availability" value="maybe"><button class="availability-option {{ $donor->availability === 'maybe' ? 'selected maybe' : '' }}" type="submit"><b>সম্ভব হলে সাহায্য করব</b><small>Maybe</small></button></form>
                    <form method="POST" action="{{ route('donor.availability') }}">@csrf<input type="hidden" name="availability" value="unavailable"><button class="availability-option {{ $donor->availability === 'unavailable' ? 'selected unavailable' : '' }}" type="submit"><b>এখন পারব না</b><small>Unavailable</small></button></form>
                </div>
            </div>

            <div class="dashboard-card impact-card"><div class="card-heading"><h2>আমার Impact</h2></div><div class="impact-number">{{ $donor->donation_count }}</div><p>টি donation recorded</p><div class="impact-note">একটি donation-ও কারও জীবনের জন্য অনেক বড় সাহায্য।</div></div>

            <div class="dashboard-card">
                <div class="card-heading"><h2>আমার Profile</h2><span>Private</span></div>
                <form method="POST" action="{{ route('donor.profile') }}" class="dashboard-form">@csrf
                    <label>District<input name="district" value="{{ old('district', $donor->district) }}" required></label>
                    <label>Area<input name="area" value="{{ old('area', $donor->area) }}" placeholder="এলাকা / থানা"></label>
                    <button class="btn btn-primary" type="submit">Profile Update</button>
                </form>
            </div>

            <div class="dashboard-card"><div class="card-heading"><h2>Donation History</h2><span>Coming next</span></div><div class="empty-state"><div>🩸</div><strong>Donation history এখনো চালু হয়নি</strong><p>Donation completion flow তৈরি হলে verified completion record যুক্ত হবে।</p></div></div>
        </div>

        <div class="privacy-banner"><strong>🔒 আপনার তথ্য private</strong><span>আপনার mobile number public donor search-এ দেখানো হয় না। Accept করলে RED BAG volunteer-এর যোগাযোগের জন্য সম্মতি সংরক্ষিত হয়; নম্বর requester-কে সরাসরি দেওয়া হয় না।</span></div>
    </div>
</section>
@endsection
