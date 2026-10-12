@extends('layouts.app')
@section('title','Request Review — RED BAG')
@section('content')
<section class="simple-page"><div class="container">
<div class="section-heading left"><span class="eyebrow">Restricted volunteer area</span><h1>Blood request review</h1><p>ফোন যাচাই কেবল নম্বরের নিয়ন্ত্রণ যাচাই করে; আবেদনটি বাস্তব কি না তা মানবিকভাবে review করতে হবে। তালিকায় requester-এর ফোন নম্বর প্রকাশ করা হয় না।</p></div>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="dashboard-head"><p>Signed in as <strong>{{ session('volunteer_email') }}</strong></p><form method="POST" action="{{ route('volunteer.logout') }}">@csrf<button class="btn btn-soft" type="submit">Logout</button></form></div>
<h2>Review queue</h2>
@if($requests->isEmpty())<div class="alert">এখন review করার মতো active pending request নেই। এটি donor availability বা রক্তের প্রয়োজন না থাকার প্রমাণ নয়।</div>
@else
<div class="request-grid">
@foreach($requests as $bloodRequest)
<article class="request-card">
<div class="blood-badge">{{ $bloodRequest->blood_group }}</div><span class="urgency">{{ $bloodRequest->urgency }}</span>
<h3>{{ $bloodRequest->units }} bag · {{ $bloodRequest->hospital_name }}</h3>
<p>{{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
<p>Requester: {{ $bloodRequest->requester_name }}</p>
<p>Phone verification: <strong>{{ $bloodRequest->requester_phone_verified_at ? 'Verified' : 'Not verified' }}</strong></p>
<p>Submitted: {{ $bloodRequest->created_at?->format('d M Y, h:i A') }}</p>
@if($bloodRequest->expires_at)<p>Expires: {{ $bloodRequest->expires_at->format('d M Y, h:i A') }}</p>@endif
<form method="POST" action="{{ route('volunteer.requests.review', $bloodRequest) }}" class="request-form">@csrf
<label>Review note / reason<textarea name="reason" rows="2" maxlength="1000" placeholder="Reject বা অতিরিক্ত তথ্যের অনুরোধে কারণ লিখুন"></textarea></label>
<div class="hero-actions"><button class="btn btn-primary" type="submit" name="action" value="approve" @disabled(!$bloodRequest->requester_phone_verified_at)>Approve</button><button class="btn btn-soft" type="submit" name="action" value="needs_info">Needs info</button><button class="btn btn-soft" type="submit" name="action" value="reject">Reject</button></div>
@if(!$bloodRequest->requester_phone_verified_at)<small>ফোন যাচাই না হওয়া পর্যন্ত approve করা যাবে না।</small>@endif
</form>
</article>
@endforeach
</div>{{ $requests->links() }}
@endif
<h2>Reviewed requests / donor shortlist</h2>
@if($approvedRequests->isEmpty())<div class="alert">এখন shortlist তৈরির মতো reviewed request নেই।</div>
@else
<div class="request-grid">
@foreach($approvedRequests as $bloodRequest)
<article class="request-card">
<div class="blood-badge">{{ $bloodRequest->blood_group }}</div><span class="urgency">{{ $bloodRequest->urgency }}</span>
<h3>{{ $bloodRequest->units }} bag · {{ $bloodRequest->hospital_name }}</h3>
<p>{{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
<p>Status: <strong>{{ $bloodRequest->status }}</strong></p>
<p>Donors in private inbox: {{ $bloodRequest->matches_count }} · Accepted with contact consent: {{ $bloodRequest->accepted_matches_count }}</p>
@if(in_array($bloodRequest->status, ['verified','matching'], true))
<form method="POST" action="{{ route('volunteer.requests.match', $bloodRequest) }}">@csrf<button class="btn btn-primary" type="submit">Create / refresh shortlist</button></form>
@endif
@if($bloodRequest->accepted_matches_count > 0)
<a class="btn btn-soft" href="{{ route('volunteer.requests.contacts', $bloodRequest) }}">Private contact workspace</a>
@endif
<small>Shortlisting does not send SMS/push alerts or make clinical compatibility decisions.</small>
</article>
@endforeach
</div>
@endif
<p class="muted">Approval means volunteer review only; it does not mean a donor is found, medical eligibility is confirmed, or donation is completed.</p>
</div></section>
@endsection
