@extends('layouts.app')
@section('title','Private Contact Coordination — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow">
<span class="eyebrow">Restricted volunteer area • Private data</span>
<h1>Contact coordination</h1>
<p>শুধু এই অনুমোদিত volunteer session-এ আবেদনকারীর ফোন এবং যারা volunteer contact-এ স্পষ্ট সম্মতি দিয়েছেন সেই donor-দের ফোন দেখানো হয়। ফোন নম্বর requester বা donor-এর মধ্যে সরাসরি share করবেন না। প্রতিটি page view এবং contact outcome audit history-তে নথিভুক্ত হয়।</p>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<a class="btn btn-soft" href="{{ route('volunteer.requests') }}">← Review queue</a>
<div class="dashboard-card">
<h2>{{ $bloodRequest->request_code }} · {{ $bloodRequest->blood_group }}</h2>
<p>{{ $bloodRequest->units }} bag · {{ $bloodRequest->hospital_name }}</p>
<p>{{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
<h3>Requester contact</h3>
<p><strong>{{ $bloodRequest->requester_name }}</strong></p>
<p><a href="tel:{{ $bloodRequest->requester_phone }}">{{ $bloodRequest->requester_phone }}</a></p>
<form method="POST" action="{{ route('volunteer.requests.contacts.log', $bloodRequest) }}" class="request-form">
@csrf<input type="hidden" name="target" value="requester">
<label>Contact outcome<select name="outcome" required><option value="called">Call attempted</option><option value="connected">Connected</option><option value="unreachable">Unreachable</option><option value="follow_up">Follow-up needed</option></select></label>
<button class="btn btn-primary" type="submit">Log requester contact</button>
</form>
</div>
<h2>Donors who consented to volunteer contact</h2>
@if($acceptedMatches->isEmpty())
<div class="alert">এখনো কোনো donor accept করে volunteer contact-এ সম্মতি দেননি।</div>
@else
@foreach($acceptedMatches as $match)
<div class="dashboard-card">
<h3>{{ $match->donor->name }} · {{ $match->donor->donor_code }}</h3>
<p>{{ $match->donor->blood_group }} · {{ $match->donor->district }}@if($match->donor->area), {{ $match->donor->area }}@endif</p>
<p><a href="tel:{{ $match->donor->phone }}">{{ $match->donor->phone }}</a></p>
<p><small>Volunteer contact consent recorded: {{ $match->contact_consent_at?->format('d M Y, h:i A') }}</small></p>
<form method="POST" action="{{ route('volunteer.requests.contacts.log', $bloodRequest) }}" class="request-form">
@csrf<input type="hidden" name="target" value="donor"><input type="hidden" name="match_id" value="{{ $match->id }}">
<label>Contact outcome<select name="outcome" required><option value="called">Call attempted</option><option value="connected">Connected</option><option value="unreachable">Unreachable</option><option value="follow_up">Follow-up needed</option></select></label>
<button class="btn btn-primary" type="submit">Log donor contact</button>
</form>
</div>
@endforeach
@endif
<p class="muted">This workspace coordinates phone calls only. It does not confirm clinical eligibility or donation completion. Never put phone numbers in notes or share them directly without a separate, explicit consent process.</p>
</div></section>
@endsection
