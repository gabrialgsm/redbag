@extends('layouts.app')
@section('title','RED BAG — রক্তের সাহায্য, সহজে')
@section('content')
<section class="service-hero">
    <div class="container service-hero-grid">
        <div class="service-hero-copy">
            <span class="service-kicker"><span class="service-kicker-dot"></span> মানুষের পাশে মানুষ</span>
            <h1>রক্তের প্রয়োজন?<br><span>শুরু করুন এখান থেকে।</span></h1>
            <p>জটিলতা নয়, সহজ সাহায্য। রক্তের অনুরোধ জমা দিন, যাচাইকৃত জরুরি অনুরোধ দেখুন, অথবা রক্তদাতা হিসেবে যুক্ত হোন।</p>
            <a class="service-main-cta" href="{{ route('blood.request') }}"><span class="service-cta-icon">＋</span><span><strong>আমার রক্ত প্রয়োজন</strong><small>সহজ ফর্মে অনুরোধ জমা দিন</small></span><span class="service-cta-arrow">→</span></a>
            <div class="service-hero-foot"><span>✓ বাংলা ভাষায় সহজ ব্যবহার</span><span>✓ ব্যক্তিগত তথ্যের সুরক্ষা</span></div>
        </div>
        <div class="service-hero-art" aria-hidden="true">
            <div class="service-art-ring ring-one"></div><div class="service-art-ring ring-two"></div>
            <div class="service-art-drop"><span>♥</span></div>
            <div class="service-art-note note-top"><span class="service-note-icon">♥</span><span><strong>রক্তদান</strong><small>মানবিকতার বন্ধন</small></span></div>
            <div class="service-art-note note-bottom"><span class="service-note-icon">＋</span><span><strong>সাহায্যের শুরু</strong><small>একটি সহজ পদক্ষেপ</small></span></div>
        </div>
    </div>
</section>

<section class="service-actions-section">
    <div class="container">
        <div class="service-section-intro"><span class="eyebrow">আপনার প্রয়োজন বেছে নিন</span><h2>কীভাবে সাহায্য করতে পারি?</h2><p>যে কাজটি করতে চান, সেটিতে চাপ দিন।</p></div>
        <div class="service-action-grid">
            <a class="service-action-card action-request" href="{{ route('blood.request') }}"><span class="service-action-symbol">＋</span><span class="service-action-copy"><strong>রক্তের অনুরোধ করুন</strong><small>রোগীর জন্য প্রয়োজনীয় তথ্য দিন</small></span><span class="service-action-arrow">→</span></a>
            <a class="service-action-card" href="{{ route('emergency.requests') }}"><span class="service-action-symbol symbol-alert">!</span><span class="service-action-copy"><strong>জরুরি অনুরোধ দেখুন</strong><small>যাচাইকৃত সক্রিয় অনুরোধের তালিকা</small></span><span class="service-action-arrow">→</span></a>
            <a class="service-action-card" href="{{ route('donor.register') }}"><span class="service-action-symbol symbol-donor">♥</span><span class="service-action-copy"><strong>আমি রক্ত দিতে চাই</strong><small>রক্তদাতা হিসেবে যুক্ত হোন</small></span><span class="service-action-arrow">→</span></a>
        </div>
    </div>
</section>

<section class="service-emergency-section">
    <div class="container">
        <div class="service-list-heading"><div><span class="eyebrow">সাহায্যের প্রয়োজন</span><h2>জরুরি রক্তের অনুরোধ</h2><p>শুধু ফোন-যাচাইকৃত এবং volunteer review-এ অনুমোদিত সক্রিয় অনুরোধ।</p></div><a class="service-view-all" href="{{ route('emergency.requests') }}">সব অনুরোধ দেখুন <span>→</span></a></div>
        @if($emergencyRequests->isEmpty())
            <div class="service-no-requests"><span class="service-no-requests-icon">♥</span><div><strong>এই মুহূর্তে তালিকায় কোনো যাচাইকৃত জরুরি অনুরোধ নেই</strong><p>নতুন অনুরোধ যাচাই হলে এখানে দেখানো হবে। জরুরি প্রয়োজনে হাসপাতাল বা অনুমোদিত রক্তদান সেবায় সরাসরি যোগাযোগ করুন।</p></div></div>
        @else
            <div class="service-emergency-grid">
                @foreach($emergencyRequests as $bloodRequest)
                    <article class="service-emergency-card">
                        <div class="service-emergency-card-top"><span class="blood-badge">{{ $bloodRequest->blood_group }}</span><span class="service-urgency {{ $bloodRequest->urgency === 'emergency' ? 'is-emergency' : '' }}">{{ $bloodRequest->urgency === 'emergency' ? 'অতি জরুরি' : 'জরুরি' }}</span></div>
                        <h3>{{ $bloodRequest->units }} ব্যাগ {{ $bloodRequest->blood_group }} রক্ত প্রয়োজন</h3>
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
        <div class="service-section-intro"><span class="eyebrow">সহজ ধাপে</span><h2>RED BAG কীভাবে কাজ করে?</h2><p>সহজভাবে শুরু করুন, পরবর্তী পদক্ষেপ স্পষ্টভাবে জানুন।</p></div>
        <div class="service-how-grid">
            <article><span>১</span><h3>অনুরোধ জমা দিন</h3><p>রক্তের গ্রুপ, হাসপাতাল ও প্রয়োজনের সময় জানান।</p></article>
            <article><span>২</span><h3>ফোন যাচাই ও review</h3><p>ফোন যাচাই এবং অনুরোধের মানবিক পর্যালোচনা আলাদা ধাপ।</p></article>
            <article><span>৩</span><h3>স্বেচ্ছাসেবী সমন্বয়</h3><p>উপযুক্ত donor সাড়া দিলে সমন্বয়ের চেষ্টা করা হবে; রক্ত পাওয়া নিশ্চিত নয়।</p></article>
        </div>
    </div>
</section>

<section class="service-trust-section">
    <div class="container service-trust-inner">
        <div class="service-trust-mark">♥</div><div><span class="eyebrow">নিরাপত্তা আগে</span><h2>সহজ সেবা, দায়িত্বশীলভাবে</h2><p>ব্যক্তিগত ফোন নম্বর প্রকাশ্যে দেখানো হয় না। ফোন যাচাই মানেই অনুরোধের সত্যতা বা donation নিশ্চিত হওয়া নয়।</p></div><a class="btn btn-primary" href="{{ route('safety') }}">নিরাপত্তা সম্পর্কে জানুন →</a>
    </div>
</section>

<section class="service-bottom-cta"><div class="container"><span class="eyebrow">আপনিও পাশে দাঁড়াতে পারেন</span><h2>আজই রক্তদাতা হিসেবে যুক্ত হোন।</h2><p>আপনার দেওয়া তথ্য যাচাইয়ের মাধ্যমে নিরাপদ রক্তদাতা নেটওয়ার্ক গড়ে তুলতে সাহায্য করুন।</p><a class="btn btn-white btn-large" href="{{ route('donor.register') }}">রক্তদাতা হিসেবে নিবন্ধন করুন →</a></div></section>
@endsection
