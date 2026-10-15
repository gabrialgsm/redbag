@extends('layouts.app')
@section('title','রক্তদাতা খুঁজুন — RED BAG')
@section('content')
<section class="simple-page find-donor-page"><div class="container narrow">
    <span class="eyebrow">রক্তদাতা খুঁজুন</span>
    <h1>সঠিক সাহায্য খোঁজার প্রথম ধাপ</h1>
    <p class="find-donor-lead">রক্তের গ্রুপ, হাসপাতাল ও প্রয়োজনের তথ্য দিয়ে অনুরোধ জমা দিন। ফোন যাচাই ও volunteer review-এর পর volunteer উপযুক্ত donor shortlist করতে পারেন।</p>
    <div class="find-donor-steps">
        <article><span>১</span><div><strong>রক্তের তথ্য দিন</strong><p>রক্তের গ্রুপ, হাসপাতাল, জেলা এবং কখন প্রয়োজন—জানান।</p></div></article>
        <article><span>২</span><div><strong>ফোন যাচাই ও review</strong><p>আবেদনকারীর ফোন যাচাই এবং volunteer-এর আবেদন review আলাদা ধাপ।</p></div></article>
        <article><span>৩</span><div><strong>Donor সমন্বয়ের চেষ্টা</strong><p>Volunteer একই blood group ও জেলায় available, verified donor shortlist করতে পারেন। এটি রক্ত পাওয়া নিশ্চিত করে না।</p></div></article>
    </div>
    <div class="find-donor-actions">
        <a class="btn btn-primary btn-large" href="{{ route('blood.request') }}">রক্তের অনুরোধ করুন →</a>
        <a class="btn btn-soft btn-large" href="{{ route('emergency.requests') }}">যাচাইকৃত জরুরি অনুরোধ দেখুন</a>
    </div>
    <p class="muted">Donor-এর ব্যক্তিগত ফোন নম্বর public directory-তে দেখানো হয় না। বর্তমানে automated SMS/push notification নেই। জরুরি প্রয়োজনে হাসপাতাল বা অনুমোদিত রক্তদান সেবার সঙ্গেও সরাসরি যোগাযোগ করুন।</p>
</div></section>
@endsection
