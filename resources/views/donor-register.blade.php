@extends('layouts.app')
@section('title','রক্ত দিন — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow">
<span class="eyebrow">Become a RED BAG Donor</span>
<h1>আজই একজন রক্তদাতা হোন</h1>
<p>Registration সহজ। কয়েকটি তথ্য দিয়ে শুরু করুন। আপনার মোবাইল verification-এর পর donor profile সক্রিয় হবে।</p>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error"><strong>অনুগ্রহ করে তথ্যগুলো ঠিক করুন:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="request-form" method="POST" action="{{ route('donor.store') }}">
@csrf
<label>আপনার রক্তের গ্রুপ<select name="blood_group" required><option value="">নির্বাচন করুন</option>@foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)<option value="{{ $group }}" @selected(old('blood_group')===$group)>{{ $group }}</option>@endforeach</select></label>
<label>আপনার নাম<input name="name" value="{{ old('name') }}" type="text" placeholder="পূর্ণ নাম" required></label>
<label>আপনার জেলা<input name="district" value="{{ old('district') }}" type="text" placeholder="যেমন: ঢাকা" required></label>
<label>আপনার এলাকা<input name="area" value="{{ old('area') }}" type="text" placeholder="এলাকা / থানা"></label>
<label>মোবাইল নম্বর<input name="phone" value="{{ old('phone') }}" type="tel" inputmode="numeric" placeholder="01XXXXXXXXX" required></label>
<label class="checkbox-row"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required><span>আমি RED BAG-এর donor network-এ যুক্ত হতে এবং প্রয়োজনের সময় রক্তদানের অনুরোধ পেতে সম্মত।</span></label>
<button class="btn btn-primary btn-large" type="submit">রেজিস্টার করুন →</button>
</form></div></section>
@endsection