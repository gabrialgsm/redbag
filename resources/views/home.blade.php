@extends('layouts.app')
@section('title','RED BAG — রক্তের সাহায্য, সহজে')
@section('content')
<section class="service-hero service-hero-compact">
    <div class="container service-hero-grid">
        <div class="service-hero-copy">
            <span class="service-kicker"><span class="service-kicker-dot"></span> মানুষের পাশে মানুষ</span>
            <h1>রক্তের প্রয়োজনে<br><span>পাশে আছে RED BAG.</span></h1>
            <p>রক্তের অনুরোধ করুন, donor খোঁজার প্রক্রিয়া শুরু করুন, অন্যের অনুরোধে সাড়া দিন—অথবা নিজেই একজন রক্তদাতা হয়ে উঠুন।</p>
            <div class="service-hero-foot"><span>✓ সহজ বাংলা অভিজ্ঞতা</span><span>✓ ব্যক্তিগত তথ্যের সুরক্ষা</span></div>
        </div>
        <div class="service-hero-art" aria-hidden="true">
            <div class="service-art-ring ring-one"></div><div class="service-art-ring ring-two"></div>
            <div class="service-art-drop"><span>♥</span></div>
            <div class="service-art-note note-top"><span class="service-note-icon">♥</span><span><strong>একটু সাহায্য</strong><small>বাঁচাতে পারে জীবন</small></span></div>
            <div class="service-art-note note-bottom"><span class="service-note-icon">＋</span><span><strong>আপনিও হতে পারেন</strong><small>কারও আশার কারণ</small></span></div>
        </div>
    </div>
</section>

<section class="blood-search-section" aria-labelledby="blood-search-title">
    <div class="container blood-search-layout">
        <div class="blood-search-intro"><span class="eyebrow">দ্রুত খুঁজুন</span><h2 id="blood-search-title">কোথায়, কোন গ্রুপের রক্ত প্রয়োজন?</h2><p>যাচাইকৃত সক্রিয় অনুরোধের তালিকা থেকে রক্তের গ্রুপ ও জেলা অনুযায়ী খুঁজুন।</p></div>
        <form class="blood-search-form" method="GET" action="{{ route('home') }}">
            <label><span>রক্তের গ্রুপ</span><select name="blood_group"><option value="">সব গ্রুপ</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(($selectedBloodGroup ?? '') === $group)>{{ $group }}</option>@endforeach</select></label>
            <label><span>জেলা</span><input type="text" name="district" value="{{ $selectedDistrict ?? '' }}" maxlength="80" placeholder="যেমন: ঢাকা, চট্টগ্রাম"></label>
            <button type="submit" class="btn btn-primary">রক্ত খুঁজুন <span aria-hidden="true">⌕</span></button>
            @if(($hasSearch ?? false))<a class="blood-search-reset" href="{{ route('home') }}">ফিল্টার মুছুন</a>@endif
        </form>
    </div>
</section>

<section class="service-actions-section service-actions-featured" id="main-services">
    <div class="container">
        <div class="service-section-intro"><span class="eyebrow">আপনার জন্য ৪টি সহজ সেবা</span><h2>আপনি কী করতে চান?</h2><p>আপনার প্রয়োজন অনুযায়ী একটি সেবা বেছে নিন।</p></div>
        <div class="service-action-grid service-action-grid-four">
            <a class="service-action-card service-action-feature action-request" href="{{ route('blood.request') }}">
                <span class="service-action-symbol">＋</span><span class="service-action-copy"><strong>রক্ত লাগবে? অনুরোধ করুন</strong><small>রোগী ও হাসপাতালের তথ্য দিয়ে রক্তের অনুরোধ জমা দিন</small><span class="service-action-link">অনুরোধ জমা দিন →</span></span>
            </a>
            <a class="service-action-card service-action-feature" href="{{ route('donor.find') }}">
                <span class="service-action-symbol symbol-search">⌕</span><span class="service-action-copy"><strong>ডোনার খুঁজুন</strong><small>রক্তের গ্রুপ ও এলাকা জানিয়ে donor খোঁজার প্রক্রিয়া শুরু করুন</small><span class="service-action-link">ডোনার খুঁজুন →</span></span>
            </a>
            <a class="service-action-card service-action-feature" href="{{ route('donor.requests') }}">
                <span class="service-action-symbol symbol-help">♥</span><span class="service-action-copy"><strong>কারও request-এ সাড়া দিন</strong><small>আপনার donor inbox-এ আসা অনুরোধে সাহায্য করতে পারবেন</small><span class="service-action-link">অনুরোধ দেখুন →</span></span>
            </a>
            <a class="service-action-card service-action-feature" href="{{ route('donor.register') }}">
                <span class="service-action-symbol symbol-donor">♡</span><span class="service-action-copy"><strong>ডোনার হোন</strong><small>রক্তদাতা হিসেবে যুক্ত হয়ে প্রয়োজনের সময় সাহায্য করুন</small><span class="service-action-link">রেজিস্টার করুন →</span></span>
            </a>
        </div>
        <p class="service-actions-note">নিরাপত্তার জন্য donor-এর ব্যক্তিগত ফোন নম্বর প্রকাশ করা হয় না। Request review ও donor shortlist-এর মাধ্যমে সমন্বয়ের চেষ্টা করা হয়—রক্ত পাওয়া নিশ্চিত নয়।</p>
    </div>
</section>



<section class="service-emergency-section">
    <div class="container">
        <div class="service-list-heading"><div><span class="eyebrow">সাহায্যের প্রয়োজন</span><h2>রক্তের অনুরোধ — জরুরি আগে</h2><p>অতি জরুরি, জরুরি, তারপর অন্যান্য সক্রিয় অনুরোধ। শুধু ফোন-যাচাইকৃত ও volunteer review-এ অনুমোদিত আবেদন দেখানো হয়।</p></div><a class="service-view-all" href="{{ route('emergency.requests') }}">সব অনুরোধ দেখুন <span>→</span></a></div>
        @if($emergencyRequests->isEmpty())
            <div class="service-no-requests"><span class="service-no-requests-icon">♥</span><div><strong>এই মুহূর্তে তালিকায় কোনো যাচাইকৃত সক্রিয় অনুরোধ নেই</strong><p>নতুন অনুরোধ যাচাই হলে এখানে দেখানো হবে। জরুরি প্রয়োজনে হাসপাতাল বা অনুমোদিত রক্তদান সেবায় সরাসরি যোগাযোগ করুন।</p></div></div>
        @else
            <div class="service-emergency-grid">
                @foreach($emergencyRequests as $bloodRequest)
                    <article class="service-emergency-card">
                        <div class="service-emergency-card-top"><span class="blood-badge">{{ $bloodRequest->blood_group }}</span><span class="service-urgency {{ $bloodRequest->urgency === 'emergency' ? 'is-emergency' : ($bloodRequest->urgency === 'urgent' ? 'is-urgent' : 'is-normal') }}">{{ $bloodRequest->urgency === 'emergency' ? '🔴 অতি জরুরি' : ($bloodRequest->urgency === 'urgent' ? '🟠 জরুরি' : 'সাধারণ') }}</span></div>
                        <h3>{{ $bloodRequest->units }} ব্যাগ {{ $bloodRequest->blood_group }} রক্ত প্রয়োজন</h3><p><span>প্রয়োজনের সময়</span><strong>{{ $bloodRequest->needed_at ? $bloodRequest->needed_at->format('d M, h:i A') : 'আবেদনকারীর সঙ্গে যাচাই করুন' }}</strong></p>
                        <p><span>হাসপাতাল</span><strong>{{ $bloodRequest->hospital_name }}</strong></p>
                        <p><span>এলাকা</span><strong>{{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</strong></p>
                        <div class="service-request-code">অনুরোধ: {{ $bloodRequest->request_code }} <span>✓ পর্যালোচিত</span></div>
                    </article>
                @endforeach
            </div>
        @endif
        <p class="service-disclaimer">তালিকায় থাকা মানেই রক্ত পাওয়া নিশ্চিত নয়। RED BAG রোগী বা আবেদনকারীর ফোন নম্বর প্রকাশ করে না।</p>
    </div>
</section>

<section class="service-how-section">
    <div class="container">
        <div class="service-section-intro"><span class="eyebrow">সহজ ধাপে</span><h2>RED BAG কীভাবে কাজ করে?</h2><p>সাহায্যের প্রতিটি ধাপ সহজ ও দায়িত্বশীল রাখার চেষ্টা করি।</p></div>
        <div class="service-how-grid">
            <article><span>১</span><h3>অনুরোধ বা donor registration</h3><p>আপনার প্রয়োজন বা রক্তদানের আগ্রহ অনুযায়ী তথ্য দিন।</p></article>
            <article><span>২</span><h3>ফোন যাচাই ও volunteer review</h3><p>ফোন যাচাই এবং আবেদন review আলাদা ধাপ; volunteer অনুরোধ যাচাই করেন।</p></article>
            <article><span>৩</span><h3>Donor সমন্বয়ের চেষ্টা</h3><p>উপযুক্ত donor shortlist হলে donor নিজের inbox-এ সাড়া দিতে পারেন।</p></article>
        </div>
    </div>
</section>

<section class="service-trust-section">
    <div class="container service-trust-inner">
        <div class="service-trust-mark">♥</div><div><span class="eyebrow">নিরাপত্তা আগে</span><h2>সহজ সেবা, দায়িত্বশীলভাবে</h2><p>ব্যক্তিগত ফোন নম্বর প্রকাশ্যে দেখানো হয় না। ফোন যাচাই মানেই অনুরোধের সত্যতা বা donation নিশ্চিত হওয়া নয়।</p></div><a class="btn btn-primary" href="{{ route('safety') }}">নিরাপত্তা সম্পর্কে জানুন →</a>
    </div>
</section>
<section class="service-bottom-cta"><div class="container"><span class="eyebrow">আপনিও পাশে দাঁড়াতে পারেন</span><h2>একজন ডোনার হয়ে কারও পাশে দাঁড়ান।</h2><p>RED BAG donor network-এ যুক্ত হয়ে মানবিক সহায়তার অংশ হোন।</p><a class="btn btn-white btn-large" href="{{ route('donor.register') }}">ডোনার হোন →</a></div></section>
@endsection
