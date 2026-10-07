@extends('layouts.app')
@section('title','রক্তের জন্য আবেদন — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow">
<span class="eyebrow">রক্ত প্রয়োজন?</span><h1>রক্তের জন্য আবেদন করুন</h1>
<p>জরুরি হলে দ্রুত request submit করুন। Verification-এর পর RED BAG কাছাকাছি available donor খুঁজে যোগাযোগের ব্যবস্থা করবে।</p>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error"><strong>অনুগ্রহ করে তথ্যগুলো ঠিক করুন:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="request-form" method="POST" action="{{ route('blood.request.store') }}">
@csrf
<label>আপনার নাম<input name="requester_name" value="{{ old('requester_name') }}" type="text" placeholder="আপনার পূর্ণ নাম" required></label>
<label>মোবাইল নম্বর<input name="requester_phone" value="{{ old('requester_phone') }}" type="tel" inputmode="numeric" placeholder="01XXXXXXXXX" required></label>
<label>রোগীর নাম<input name="patient_name" value="{{ old('patient_name') }}" type="text" placeholder="রোগীর নাম (ঐচ্ছিক)"></label>
<label>রক্তের গ্রুপ<select name="blood_group" required><option value="">নির্বাচন করুন</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(old('blood_group')===$group)>{{ $group }}</option>@endforeach</select></label>
<label>কত ব্যাগ প্রয়োজন?<input name="units" type="number" min="1" max="20" value="{{ old('units',1) }}" required></label>
<label>কতটা জরুরি?<select name="urgency" required><option value="emergency" @selected(old('urgency')==='emergency')>এখনই — জরুরি</option><option value="urgent" @selected(old('urgency')==='urgent')>আজ — জরুরি</option><option value="normal" @selected(old('urgency')==='normal')>সাধারণ</option></select></label>
<label>হাসপাতাল<input name="hospital_name" value="{{ old('hospital_name') }}" type="text" placeholder="হাসপাতালের নাম" required></label>
<label>জেলা<input name="district" value="{{ old('district') }}" type="text" placeholder="জেলা" required></label>
<label>এলাকা<input name="area" value="{{ old('area') }}" type="text" placeholder="এলাকা / থানা"></label>
<label>কখন প্রয়োজন?<input name="needed_at" value="{{ old('needed_at') }}" type="datetime-local"></label>
<button class="btn btn-primary btn-large" type="submit">REQUEST BLOOD →</button>
</form></div></section>
@endsection