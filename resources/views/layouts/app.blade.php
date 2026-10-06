<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#e31b23"><meta name="description" content="RED BAG — A Bag of Life. রক্তের প্রয়োজন হলে সাহায্য খুঁজুন, রক্তদাতা হিসেবে যুক্ত হোন।">
<title>@yield('title','RED BAG — A Bag of Life')</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="site-header"><div class="container nav-wrap">
<a href="{{ route('home') }}" class="brand"><img src="/images/redbag-logo.svg" alt="RedBag logo"></a>
<nav class="desktop-nav"><a class="active" href="{{ route('home') }}">হোম</a><a href="{{ route('blood.request') }}">রক্তের জন্য আবেদন</a><a href="{{ route('donor.register') }}">রক্ত দিন</a><a href="{{ route('campaigns') }}">ক্যাম্পেইন</a><a href="{{ route('about') }}">আমাদের সম্পর্কে</a><a href="#contact">যোগাযোগ</a></nav>
<div class="nav-actions"><button class="icon-btn" aria-label="Search">⌕</button><a class="btn btn-primary btn-small" href="{{ route('donor.register') }}">লগইন / রেজিস্টার</a></div>
</div></header>
<main>@yield('content')</main>
<footer id="contact" class="site-footer"><div class="container footer-grid">
<div><a href="{{ route('home') }}" class="brand"><img src="/images/redbag-logo.svg" alt="RedBag logo"></a><p>জাতি, ধর্ম ও দল নির্বিশেষে—রক্ত দিবো হেসে হেসে। মানুষের পাশে RED BAG.</p></div>
<div><h3>দ্রুত লিংক</h3><a href="{{ route('blood.request') }}">রক্তের জন্য আবেদন</a><a href="{{ route('donor.register') }}">রক্ত দিন</a><a href="{{ route('campaigns') }}">ক্যাম্পেইন</a></div>
<div><h3>RED BAG</h3><a href="{{ route('about') }}">আমাদের সম্পর্কে</a><a href="#">গোপনীয়তা</a><a href="#">শর্তাবলি</a><a href="#">FAQ</a></div>
<div><h3>আপডেট পেতে চান?</h3><p>RED BAG-এর গুরুত্বপূর্ণ আপডেট পেতে যুক্ত থাকুন।</p><div class="subscribe-form"><input type="email" placeholder="আপনার ইমেইল"><button type="button">সাবস্ক্রাইব</button></div></div>
</div><div class="container footer-bottom"><span>© {{ date('Y') }} RED BAG. সর্বস্বত্ব সংরক্ষিত।</span><span>A BAG OF LIFE • মানুষ মানুষের জন্য • ❤️</span></div></footer>
</body></html>