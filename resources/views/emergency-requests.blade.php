@extends('layouts.app')
@section('title','জরুরি রক্তের অনুরোধ — RED BAG')
@section('content')
<section class="page-section"><div class="container">
    <div class="section-heading left">
        <span class="eyebrow">জরুরি রক্তের অনুরোধ</span>
        <h1>যেখানে এখন সাহায্য প্রয়োজন</h1>
        <p>শুধু ফোন-যাচাইকৃত ও volunteer review-এ অনুমোদিত, সক্রিয় জরুরি/দ্রুত অনুরোধ দেখানো হয়। তালিকায় থাকা মানেই রক্তের ব্যবস্থা নিশ্চিত নয়।</p>
    </div>
    @if($emergencyRequests->isEmpty())
        <div class="public-empty-state">
            <div class="public-empty-icon">♥</div>
            <h2>এই মুহূর্তে দেখানোর মতো যাচাইকৃত জরুরি অনুরোধ নেই</h2>
            <p>নতুন অনুরোধ যাচাই হলে এখানে দেখা যাবে। জরুরি প্রয়োজনে নিকটস্থ হাসপাতাল বা অনুমোদিত রক্তদান সেবার সঙ্গে সরাসরি যোগাযোগ করুন।</p>
            <a class="btn btn-primary btn-large" href="{{ route('blood.request') }}">রক্তের অনুরোধ করুন</a>
        </div>
    @else
        <div class="public-request-list">
        @foreach($emergencyRequests as $bloodRequest)
            <article class="public-request-card">
                <div class="public-request-top">
                    <span class="blood-badge">{{ $bloodRequest->blood_group }}</span>
                    <span class="public-urgency {{ $bloodRequest->urgency === 'emergency' ? 'is-emergency' : '' }}">{{ $bloodRequest->urgency === 'emergency' ? 'অতি জরুরি' : 'জরুরি' }}</span>
                </div>
                <h2>{{ $bloodRequest->units }} ব্যাগ {{ $bloodRequest->blood_group }} রক্ত প্রয়োজন</h2>
                <p><strong>হাসপাতাল:</strong> {{ $bloodRequest->hospital_name }}</p>
                <p><strong>এলাকা:</strong> {{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
                <p><strong>প্রয়োজন:</strong> {{ $bloodRequest->needed_at ? $bloodRequest->needed_at->format('d M, h:i A') : 'সময় আবেদনকারীর সঙ্গে যাচাই করুন' }}</p>
                <div class="public-request-foot"><span>অনুরোধের কোড: {{ $bloodRequest->request_code }}</span><span>পর্যালোচিত</span></div>
            </article>
        @endforeach
        </div>
        <div class="public-pagination">{{ $emergencyRequests->links() }}</div>
    @endif
    <div class="public-safety-note"><strong>গুরুত্বপূর্ণ:</strong> RED BAG অনুরোধ ও স্বেচ্ছাসেবী সমন্বয়ে সহায়তা করে; রক্ত পাওয়া, donor availability বা donation নিশ্চিত করে না। রোগীর ব্যক্তিগত তথ্য ও ফোন নম্বর প্রকাশ করা হয় না।</div>
</div></section>
@endsection
