@extends('layouts.app')
@section('title','Donor Request Inbox — RED BAG')
@section('content')
<section class="simple-page"><div class="container">
<span class="eyebrow">RED BAG • Donor inbox</span>
<h1>আপনার কাছে আসা request</h1>
<p>Volunteer review-এর পর shortlist-এ যুক্ত request এখানে দেখা যায়। বর্তমানে SMS বা push notification পাঠানো হয় না।</p>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<a class="btn btn-soft" href="{{ route('donor.dashboard') }}">← Dashboard</a>
@if($matches->isEmpty())
<div class="dashboard-card"><h2>এখন কোনো active request নেই</h2><p>এর মানে এই নয় যে রক্তের প্রয়োজন নেই। নতুন shortlist হলে login করে এখানে দেখতে পারবেন।</p></div>
@else
<div class="request-grid">
@foreach($matches as $match)
@php($bloodRequest = $match->bloodRequest)
<article class="request-card">
<div class="blood-badge">{{ $bloodRequest->blood_group }}</div><span class="urgency">{{ $bloodRequest->urgency }}</span>
<h3>{{ $bloodRequest->units }} bag প্রয়োজন</h3>
<p>Hospital: {{ $bloodRequest->hospital_name }}</p>
<p>District: {{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
@if($bloodRequest->needed_at)<p>Needed at: {{ $bloodRequest->needed_at->format('d M Y, h:i A') }}</p>@endif
<p>Response: <strong>{{ $match->response }}</strong></p>
<p>Request ID: {{ $bloodRequest->request_code }}</p>
@if($match->response === 'pending' || $match->response === 'maybe')
<form method="POST" action="{{ route('donor.requests.respond', $match) }}" class="request-form">
@csrf
<input type="hidden" name="response" value="accepted">
<label class="checkbox-row"><input type="checkbox" name="contact_consent" value="1" required><span>আমি সম্মত যে RED BAG volunteer এই request নিয়ে সমন্বয়ের জন্য আমার সঙ্গে যোগাযোগ করতে পারবেন। আমার ফোন নম্বর requester-কে public বা সরাসরি দেওয়া হবে না।</span></label>
<button class="btn btn-primary btn-full" type="submit">আমি সাহায্য করতে পারি</button>
</form>
<div class="hero-actions">
<form method="POST" action="{{ route('donor.requests.respond', $match) }}">@csrf<input type="hidden" name="response" value="maybe"><button class="btn btn-soft" type="submit">সম্ভব হলে</button></form>
<form method="POST" action="{{ route('donor.requests.respond', $match) }}">@csrf<input type="hidden" name="response" value="declined"><button class="btn btn-soft" type="submit">পারব না</button></form>
</div>
@endif
</article>
@endforeach
</div>
{{ $matches->links() }}
@endif
<p class="muted">রক্তের গ্রুপ/এলাকা অনুযায়ী shortlist একটি coordination aid, clinical compatibility decision নয়। Donation-এর আগে hospital/authorized service-এর নির্দেশনা অনুসরণ করুন।</p>
</div></section>
@endsection
