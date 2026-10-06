@extends('layouts.app')
@section('title','রক্ত দিন — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow"><span class="eyebrow">Become a RED BAG Donor</span><h1>আজই একজন রক্তদাতা হোন</h1><p>Registration সহজ। প্রথমে শুধু blood group, location এবং mobile verification দিয়ে শুরু করুন।</p><form class="request-form">
<label>আপনার রক্তের গ্রুপ<select><option>জানি না</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></label>
<label>আপনার নাম<input type="text" placeholder="পূর্ণ নাম"></label><label>আপনার এলাকা<input type="text" placeholder="এলাকা / জেলা"></label><label>মোবাইল নম্বর<input type="tel" placeholder="01XXXXXXXXX"></label>
<button class="btn btn-primary btn-large" type="button">OTP দিয়ে রেজিস্টার করুন →</button></form></div></section>
@endsection