@extends('layouts.app')
@section('title','রক্তের জন্য আবেদন — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow"><span class="eyebrow">রক্ত প্রয়োজন?</span><h1>রক্তের জন্য আবেদন করুন</h1><p>জরুরি হলে দ্রুত request submit করুন। RED BAG কাছাকাছি available donor খুঁজে যোগাযোগের ব্যবস্থা করবে।</p><form class="request-form">
<label>রক্তের গ্রুপ<select><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></label>
<label>কত ব্যাগ প্রয়োজন?<input type="number" min="1" value="1"></label>
<label>হাসপাতাল<input type="text" placeholder="হাসপাতালের নাম"></label>
<label>এলাকা<input type="text" placeholder="এলাকা / জেলা"></label>
<label>কখন প্রয়োজন?<select><option>এখনই — জরুরি</option><option>আজ</option><option>আগামীকাল</option><option>নির্দিষ্ট সময়</option></select></label>
<label>আপনার মোবাইল নম্বর<input type="tel" placeholder="01XXXXXXXXX"></label>
<button class="btn btn-primary btn-large" type="button">REQUEST BLOOD →</button></form></div></section>
@endsection