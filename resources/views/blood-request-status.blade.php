@extends('layouts.app')
@section('title','আবেদনের অবস্থা — RED BAG')
@section('content')
<section class="simple-page"><div class="container narrow">
<span class="eyebrow">Private request tracking</span>
<h1>আবেদনের অবস্থা</h1>
@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
@php
$statusLabel = $isExpired
    ? 'মেয়াদ শেষ — আবেদনটি আর সক্রিয় নয়'
    : match($bloodRequest->status) {
        'pending_verification' => $bloodRequest->requester_phone_verified_at ? 'Volunteer review অপেক্ষমাণ' : 'ফোন যাচাই অপেক্ষমাণ',
        'needs_info' => 'অতিরিক্ত তথ্য প্রয়োজন',
        'verified' => 'Volunteer review সম্পন্ন — donor পাওয়া নিশ্চিত নয়',
        'matching' => 'সমন্বয়ের পর্যায়ে',
        'matched' => 'Donor response পাওয়া গেছে — donation নিশ্চিত নয়',
        'completed' => 'সম্পন্ন হিসেবে নিশ্চিত করা হয়েছে',
        'cancelled' => 'আবেদন বন্ধ / বাতিল',
        'rejected' => 'আবেদন গ্রহণ করা হয়নি',
        default => 'অবস্থা আপডেট হচ্ছে',
    };
$eventLabels = [
    'request_submitted' => 'আবেদন জমা হয়েছে',
    'requester_phone_verified' => 'ফোন নম্বর যাচাই হয়েছে',
    'request_approved' => 'Volunteer review সম্পন্ন হয়েছে',
    'request_rejected' => 'Review-এর পর আবেদন বন্ধ করা হয়েছে',
    'more_information_requested' => 'অতিরিক্ত তথ্য প্রয়োজন',
    'requester_cancelled' => 'আবেদনকারী আবেদন বাতিল করেছেন',
];
@endphp
<div class="dashboard-card">
    <div class="card-heading"><h2>{{ $bloodRequest->request_code }}</h2><span>{{ $bloodRequest->blood_group }}</span></div>
    <h3>{{ $statusLabel }}</h3>
    <p>প্রয়োজন: {{ $bloodRequest->units }} ব্যাগ · {{ $bloodRequest->urgency === 'emergency' ? 'জরুরি' : ($bloodRequest->urgency === 'urgent' ? 'দ্রুত' : 'সাধারণ') }}</p>
    <p>হাসপাতাল: {{ $bloodRequest->hospital_name }}</p>
    <p>এলাকা: {{ $bloodRequest->district }}@if($bloodRequest->area), {{ $bloodRequest->area }}@endif</p>
    <p>জমা: {{ $bloodRequest->created_at?->format('d M Y, h:i A') }}</p>
    @if($bloodRequest->expires_at)<p>আবেদনের সময়সীমা: {{ $bloodRequest->expires_at->format('d M Y, h:i A') }}</p>@endif
</div>
<div class="dashboard-card">
    <h2>অগ্রগতির ইতিহাস</h2>
    @if($events->isEmpty())
        <p>এখনো কোনো status update নেই।</p>
    @else
        <ul>
        @foreach($events as $event)
            <li>{{ $eventLabels[$event->event] ?? 'আবেদনের অবস্থা আপডেট হয়েছে' }} — {{ $event->created_at?->format('d M Y, h:i A') }}</li>
        @endforeach
        </ul>
    @endif
</div>
@if(!$isExpired && in_array($bloodRequest->status, ['pending_verification', 'needs_info', 'verified'], true))
<form method="POST" action="{{ route('blood.request.cancel', ['requestCode' => $bloodRequest->request_code]) }}" class="request-form">
    @csrf
    <input type="hidden" name="token" value="{{ $trackingToken }}">
    <button class="btn btn-soft" type="submit">আমার আবেদন বাতিল করুন</button>
</form>
@endif
<p class="muted">এই private link-টি নিজের কাছে রাখুন। এতে requester বা patient-এর ফোন নম্বর প্রকাশ করা হয় না। ফোন যাচাই আবেদনটির বৈধতা প্রমাণ করে না; RED BAG চিকিৎসা পরামর্শ বা রক্ত পাওয়ার নিশ্চয়তা দেয় না।</p>
</div></section>
@endsection
