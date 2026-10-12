<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#e31b23"><meta name="description" content="RED BAG — A Bag of Life. রক্তের প্রয়োজন হলে আবেদন জমা দিন, রক্তদাতা হিসেবে যুক্ত হোন।">
<title>@yield('title','RED BAG — A Bag of Life')</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="site-header"><div class="container nav-wrap">
<a href="{{ route('home') }}" class="brand"><img src="/images/redbag-logo.svg" alt="RedBag logo"></a>
<nav class="desktop-nav"><a href="{{ route('home') }}">হোম</a><a href="{{ route('blood.request') }}">রক্তের জন্য আবেদন</a><a href="{{ route('donor.register') }}">রক্ত দিন</a><a href="{{ route('campaigns') }}">ক্যাম্পেইন</a><a href="{{ route('about') }}">আমাদের সম্পর্কে</a><a href="#contact">যোগাযোগ</a></nav>
<div class="nav-actions">@if(session('donor_id'))<a class="btn btn-primary btn-small" href="{{ route('donor.dashboard') }}">আমার Dashboard</a>@else<a class="btn btn-primary btn-small" href="{{ route('donor.register') }}">রেজিস্টার</a>@endif</div>
</div></header>
@if(session('success'))<div class="container notice notice-info" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="container notice notice-info" role="alert">{{ session('error') }}</div>@endif
<main>@yield('content')</main>
<footer id="contact" class="site-footer"><div class="container footer-grid">
<div><a href="{{ route('home') }}" class="brand"><img src="/images/redbag-logo.svg" alt="RedBag logo"></a><p>জাতি, ধর্ম ও দল নির্বিশেষে—মানুষের পাশে RED BAG.</p></div>
<div><h3>দ্রুত লিংক</h3><a href="{{ route('blood.request') }}">রক্তের জন্য আবেদন</a><a href="{{ route('donor.register') }}">রক্তদাতা নিবন্ধন</a><a href="{{ route('campaigns') }}">ক্যাম্পেইন</a></div>
<div><h3>RED BAG</h3><a href="{{ route('about') }}">আমাদের সম্পর্কে</a><a href="{{ route('safety') }}">নিরাপত্তা ও গোপনীয়তা</a></div>
<div><h3>জরুরি সহায়তা</h3><p>জরুরি অবস্থায় নিকটস্থ হাসপাতাল বা অনুমোদিত রক্তদান সেবার সঙ্গে যোগাযোগ করুন। ওয়েবসাইটে আবেদন করাই তাৎক্ষণিক রক্ত পাওয়ার নিশ্চয়তা নয়।</p></div>
</div><div class="container footer-bottom"><span>© {{ date('Y') }} RED BAG. সর্বস্বত্ব সংরক্ষিত।</span><span>A BAG OF LIFE • মানুষ মানুষের জন্য • ❤️</span></div></footer>
</body></html>
