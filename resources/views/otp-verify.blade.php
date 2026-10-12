@extends('layouts.app')

@section('title', 'OTP Verification — RED BAG')

@section('content')
<section class="page-section">
    <div class="form-card otp-card">
        <span class="eyebrow">RED BAG • নিরাপদ যাচাই</span>
        <h1>মোবাইল নম্বর যাচাই করুন</h1>
        <p class="muted">আপনার নম্বর <strong>{{ substr($challenge->phone, 0, 3).'******'.substr($challenge->phone, -2) }}</strong>-এর জন্য তৈরি OTP দিন। OTP ৫ মিনিট কার্যকর থাকবে।</p>
        <form method="POST" action="{{ route('otp.verify', $challenge) }}" class="stack-form">
            @csrf
            <label>OTP Code
                <input name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus>
            </label>
            @error('code') <div class="field-error">{{ $message }}</div> @enderror
            @error('phone') <div class="field-error">{{ $message }}</div> @enderror
            <button class="btn btn-primary" type="submit">Verify & Continue</button>
        </form>
        <form method="POST" action="{{ route('otp.resend', $challenge) }}" class="otp-resend-form">
            @csrf
            <button class="btn btn-soft btn-full" type="submit">নতুন OTP চাই</button>
        </form>
        <div class="notice notice-info">
            <strong>সীমাবদ্ধতা:</strong> Production SMS provider এখনো যুক্ত হয়নি। OTP যাচাইয়ের আগে donor বা request-কে verified হিসেবে ধরা হবে না।
        </div>
    </div>
</section>
@endsection
