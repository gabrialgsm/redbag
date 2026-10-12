@extends('layouts.app')
@section('title','Volunteer Login — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow">
<span class="eyebrow">RED BAG • Volunteer access</span>
<h1>Volunteer login</h1>
<p>এই portal শুধুমাত্র অনুমোদিত volunteer-এর জন্য।</p>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
@php
$configured = (bool) (config('redbag.volunteer_email') && config('redbag.volunteer_password_hash'));
@endphp
@if(!$configured)
<div class="alert error">Volunteer portal এখন বন্ধ আছে। Production .env-এ REDBAG_VOLUNTEER_EMAIL এবং REDBAG_VOLUNTEER_PASSWORD_HASH সেট করার পর এটি চালু হবে।</div>
@endif
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="request-form" method="POST" action="{{ route('volunteer.login.submit') }}">
@csrf
<label>Email<input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required></label>
<label>Password<input name="password" type="password" autocomplete="current-password" required></label>
<button class="btn btn-primary btn-large" type="submit" @disabled(!$configured)>Login</button>
</form>
</div></section>
@endsection
