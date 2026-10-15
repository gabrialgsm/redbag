@extends('layouts.app')
@section('title','RED BAG — রক্তের অনুরোধ ও রক্তদাতা')
@section('content')
<section class="home-direct-section">
    <div class="container">
        <div class="home-direct-heading">
            <span class="eyebrow">মানুষের পাশে মানুষ</span>
            <h1>রক্তের প্রয়োজনে, <span>পাশে আছে RED BAG</span></h1>
            <p>রক্তের অনুরোধ করুন, অথবা রক্তদাতা হয়ে কারও পাশে দাঁড়ান।</p>
        </div>
        <div class="home-direct-actions">
            <a class="home-primary-action home-request-action" href="{{ route('blood.request') }}">
                <span class="home-action-icon" aria-hidden="true">＋</span>
                <span><strong>রক্তের জন্য আবেদন করুন</strong><small>রক্তের গ্রুপ, হাসপাতাল ও প্রয়োজনের তথ্য দিন</small></span>
                <span class="home-action-arrow" aria-hidden="true">→</span>
            </a>
            <a class="home-primary-action home-donate-action" href="{{ session('donor_id') ? route('donor.dashboard') : route('donor.register') }}">
                <span class="home-action-icon" aria-hidden="true">♥</span>
                <span><strong>{{ session('donor_id') ? 'ডোনার ড্যাশবোর্ড' : 'আমি রক্ত দিতে চাই' }}</strong><small>{{ session('donor_id') ? 'আপনার availability হালনাগাদ করুন' : 'রক্তদাতা হিসেবে যুক্ত হয়ে সাহায্য করুন' }}</small></span>
                <span class="home-action-arrow" aria-hidden="true">→</span>
            </a>
        </div>
        <nav class="home-quick-links" aria-label="অন্যান্য সেবা">
            <a href="{{ route('donor.find') }}">ডোনার খুঁজুন</a>
            <a href="{{ route('donor.requests') }}">অনুরোধে সাড়া দিন</a>
            <a href="{{ route('emergency.requests') }}">সব রক্তের অনুরোধ</a>
        </nav>
    </div>
</section>

<section class="blood-search-section" aria-labelledby="blood-search-title">
    <div class="container blood-search-layout">
        <div class="blood-search-intro"><span class="eyebrow">রক্তের অনুরোধ খুঁজুন</span><h2 id="blood-search-title">কোন গ্রুপের রক্ত, কোন জেলায়?</h2><p>ফোন-যাচাইকৃত ও পর্যালোচিত সক্রিয় অনুরোধ খুঁজুন।</p></div>
        <form class="blood-search-form" method="GET" action="{{ route('home') }}">
            <label><span>রক্তের গ্রুপ</span><select name="blood_group"><option value="">সব গ্রুপ</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(($selectedBloodGroup ?? '') === $group)>{{ $group }}</option>@endforeach</select></label>
            <label><span>জেলা</span><input type="text" name="district" value="{{ $selectedDistrict ?? '' }}" maxlength="80" placeholder="যেমন: ঢাকা, চট্টগ্রাম"></label>
            <button type="submit" class="btn btn-primary">খুঁজুন <span aria-hidden="true">⌕</span></button>
            @if(($hasSearch ?? false))<a class="blood-search-reset" href="{{ route('home') }}">ফিল্টার মুছুন</a>@endif
        </form>
    </div>
</section>

<section class="location-insights-section" id="location-search">
    <div class="container">
        <div class="home-section-heading"><div><span class="eyebrow">এলাকাভিত্তিক তথ্য</span><h2>আপনার এলাকায় রক্তদাতা ও রক্তের প্রয়োজন</h2><p>শুধু সম্মতিদানকারী, ফোন-যাচাইকৃত ও বর্তমানে রক্তদানে আগ্রহী donor-এর মোট সংখ্যা দেখানো হয়—ব্যক্তিগত তথ্য নয়।</p></div></div>
        @if(($selectedDistrict ?? '') !== '')
            <div class="location-selected-summary">
                <div><span class="location-summary-label">নির্বাচিত জেলা</span><strong>{{ $selectedDistrict }}</strong></div>
                <div><strong>{{ $districtDonorCount ?? 0 }}</strong><span>উপলভ্য যাচাইকৃত ডোনার</span></div>
                <div><strong>{{ $districtRequestCount ?? 0 }}</strong><span>সক্রিয় রক্তের অনুরোধ</span></div>
            </div>
        @endif
        @if($locationRows->isEmpty())
            <div class="location-empty-state"><strong>এলাকাভিত্তিক তথ্য এখনো পাওয়া যায়নি</strong><p>যাচাইকৃত ডোনার ও সক্রিয় অনুরোধ যুক্ত হলে এখানে জেলার তথ্য দেখা যাবে।</p></div>
        @else
            <div class="location-table-wrap">
                <table class="location-table">
                    <thead><tr><th scope="col">জেলা</th><th scope="col">উপলভ্য ডোনার</th><th scope="col">সক্রিয় অনুরোধ</th><th scope="col">খুঁজুন</th></tr></thead>
                    <tbody>
                    @foreach($locationRows as $row)
                        <tr><th scope="row">{{ $row['district'] }}</th><td><span class="location-count donor-count">{{ $row['donors'] }}</span></td><td><span class="location-count {{ $row['requests'] > 0 ? 'request-count-active' : '' }}">{{ $row['requests'] }}</span></td><td><a class="location-filter-link" href="{{ route('home', ['district' => $row['district']]) }}#location-search">এই জেলা দেখুন →</a></td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <p class="location-data-note">এগুলো জেলার ভিত্তিতে গণনা; নির্দিষ্ট দূরত্ব বা সরাসরি GPS-ভিত্তিক নিকটতম donor তালিকা নয়। ডোনারের ব্যক্তিগত ফোন নম্বর প্রকাশ করা হয় না।</p>
    </div>
</section>

<section class="live-stats-section" aria-label="RED BAG-এর বর্তমান পরিসংখ্যান">
    <div class="container">
        <div class="live-stats-heading"><span class="eyebrow">সর্বশেষ ডেটাবেস তথ্য</span><h2>RED BAG-এর বর্তমান চিত্র</h2></div>
        <div class="live-stats-grid">
            <article><span class="live-stat-icon">♥</span><strong>{{ number_format($availableDonorCount) }}</strong><span>এখন রক্ত দিতে আগ্রহী ডোনার</span></article>
            <article><span class="live-stat-icon">＋</span><strong>{{ number_format($activeRequestCount) }}</strong><span>সক্রিয় যাচাইকৃত অনুরোধ</span></article>
            <article><span class="live-stat-icon">⌖</span><strong>{{ number_format($districtCount) }}</strong><span>ডোনার বা অনুরোধ থাকা জেলা</span></article>
        </div>
        <p class="live-stats-note">পরিসংখ্যানগুলো ডেটাবেস থেকে হিসাব করা হয়; ফোন যাচাই বা donor availability পরিবর্তিত হলে সংখ্যাও পরিবর্তিত হবে।</p>
    </div>
</section>

<section class="service-emergency-section home-emergency-section">
    <div class="container">
        <div class="service-list-heading"><div><span class="eyebrow">এখন যাঁদের সাহায্য প্রয়োজন</span><h2>রক্তের অনুরোধ</h2><p>অতি জরুরি অনুরোধ আগে দেখানো হয়। শুধু ফোন-যাচাইকৃত ও volunteer review-এ অনুমোদিত সক্রিয় আবেদন এখানে আসে।</p></div><a class="service-view-all" href="{{ route('emergency.requests') }}">সব অনুরোধ দেখুন <span>→</span></a></div>
        @if($emergencyRequests->isEmpty())
            <div class="service-no-requests"><span class="service-no-requests-icon">♥</span><div><strong>এই মুহূর্তে কোনো যাচাইকৃত সক্রিয় অনুরোধ নেই</strong><p>নতুন অনুরোধ যাচাই হলে এখানে দেখা যাবে। জরুরি প্রয়োজনে নিকটস্থ হাসপাতাল বা অনুমোদিত রক্তদান সেবার সঙ্গে সরাসরি যোগাযোগ করুন।</p><a class="btn btn-primary btn-small" href="{{ route('blood.request') }}">রক্তের জন্য আবেদন করুন</a></div></div>
        @else
            <div class="service-emergency-grid">
                @foreach($emergencyRequests as $bloodRequest)
                    <article class="service-emergency-card">
                        <div class="service-emergency-card-top"><span class="blood-badge">{{ $bloodRequest->blood_group }}</span><span class="service-urgency {{ $bloodRequest->urgency === 'emergency' ? 'is-emergency' : ($bloodRequest->urgency === 'urgent' ? 'is-urgent' : 'is-normal') }}">{{ $bloodRequest->urgency === 'emergency' ? '🔴 অতি জরুরি' : ($bloodRequest->urgency === 'urgent' ? '🟠 জরুরি' : 'সাধারণ') }}</span></div>
                        <h3>{{ $bloodRequest->units }} ব্যাগ {{ $bloodRequest->blood_group }} রক্ত প্রয়োজন</h3>
                        <p><span>হাসপাতাল</span><strong>{{ $bloodRequest->hospital_name }}</strong></p>
                        <p><span>এলাকা</span><strong>{{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</strong></p>
                        <p><span>প্রয়োজনের সময়</span><strong>{{ $bloodRequest->needed_at ? $bloodRequest->needed_at->format('d M, h:i A') : 'আবেদনকারীর সঙ্গে যাচাই করুন' }}</strong></p>
                        <div class="service-request-code">অনুরোধ: {{ $bloodRequest->request_code }} <span>✓ পর্যালোচিত</span></div>
                        <a class="request-donate-action" href="{{ session('donor_id') ? route('donor.dashboard') : route('donor.register') }}"><span>♥</span><span><strong>{{ session('donor_id') ? 'ডোনার ড্যাশবোর্ড খুলুন' : 'রক্ত দিতে চান?' }}</strong><small>{{ session('donor_id') ? 'আপনার availability হালনাগাদ করুন' : 'ডোনার হিসেবে যুক্ত হয়ে সাহায্য করুন' }}</small></span><span aria-hidden="true">→</span></a>
                    </article>
                @endforeach
            </div>
        @endif
        <p class="service-disclaimer">অনুরোধটি যাচাই করা হলেও রক্ত পাওয়া নিশ্চিত নয়। নির্দিষ্ট অনুরোধে donor matching ও যোগাযোগ volunteer-এর মাধ্যমে সমন্বয় করা হয়।</p>
    </div>
</section>

<section class="home-secondary-services">
    <div class="container">
        <div class="home-section-heading"><div><span class="eyebrow">আরও সেবা</span><h2>আপনি যেভাবে সাহায্য করতে পারেন</h2></div></div>
        <div class="home-secondary-grid">
            <a href="{{ route('donor.find') }}"><strong>ডোনার খুঁজুন</strong><span>রক্তের অনুরোধ ও donor সমন্বয়ের প্রক্রিয়া জানুন →</span></a>
            <a href="{{ route('donor.requests') }}"><strong>অনুরোধে সাড়া দিন</strong><span>যাচাইকৃত donor inbox দেখুন →</span></a>
            <a href="{{ route('safety') }}"><strong>নিরাপত্তা ও গোপনীয়তা</strong><span>কীভাবে তথ্য সুরক্ষিত রাখা হয় জানুন →</span></a>
        </div>
    </div>
</section>
@endsection
