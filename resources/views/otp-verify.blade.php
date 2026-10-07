@extends('layouts.app')

@section('title', 'OTP Verification — RED BAG')

@section('content')
<section class="page-section">
    <div class="form-card otp-card">
        <span class="eyebrow">RED BAG • নিরাপদ যাচাই</span>
        <h1>মোবাইল নম্বর যাচাই করুন</h1>
        <p class="muted">আপনার নম্বর <strong>{{ $challenge->phone }}</strong>-এ পাঠানো ৬ সংখ্যার OTP দিন। OTP ৫ মিনিট কার্যকর থাকবে।</p>

        <form method="POST" action="{{ route('otp.verify', $challenge) }}" class="stack-form">
            @csrf
            <label>OTP Code
                <input name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus>
            </label>
            @error('code') <div class="field-error">{{ $message }}</div> @enderror
            <button class="btn btn-primary" type="submit">Verify & Continue</button>
        </form>

        <div class="notice notice-info">
            <strong>Development mode:</strong> SMS provider এখনো যুক্ত করা হয়নি। বর্তমান log driver OTP-টি Laravel log-এ লিখে রাখে; production-এ SMS provider যুক্ত করার পর এই flow একই থাকবে।
        </div>
    </div>
</section>
@endsection
