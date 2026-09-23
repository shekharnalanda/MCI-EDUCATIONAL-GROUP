@extends('layouts.app')

@section('title', $project['name'].' | About | MCI Educational Group')
@section('meta_description', $project['summary'])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/about-profile.css') }}">
@endpush

@section('content')
@php
    $isClientProject = $project['slug'] === 'aryans-dandiya-night';
    $isLearning = in_array($project['category'], ['Education', 'School education', 'Skills'], true);
    $isBusiness = in_array($project['category'], ['Business software', 'Business services', 'Commerce', 'Digital services'], true);
@endphp

<section class="profile-hero">
  <div class="container profile-hero-grid">
    <div class="profile-hero-copy">
      <div class="profile-eyebrow"><span></span>{{ $project['category'] }} · MCI Educational Group</div>
      <h1>{{ $project['name'] }}</h1>
      <p class="profile-hero-hindi">{{ $project['headline'] }}</p>
      <p class="profile-hero-summary">{{ $project['summary'] }}</p>
      <div class="profile-actions">
        <a href="#what-we-do" class="btn btn-primary btn-lg fw-bold">जानें हम क्या करते हैं <span aria-hidden="true">↓</span></a>
        @if($project['url'])<a href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-lg fw-bold">आधिकारिक वेबसाइट ↗</a>@endif
      </div>
    </div>
    <div class="profile-hero-art">
      @include('partials.about-illustration', ['project' => $project, 'isLearning' => $isLearning, 'isBusiness' => $isBusiness])
    </div>
  </div>
</section>

<nav class="profile-breadcrumb" aria-label="Breadcrumb"><div class="container">
  <a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('about') }}#our-network">हमारे संस्थान</a><span>/</span><span aria-current="page">{{ $project['name'] }}</span>
</div></nav>

<section class="profile-intro" id="what-we-do"><div class="container profile-intro-grid">
  <div class="profile-section-lead"><span class="profile-section-label">01 · OUR PURPOSE</span><h2>हम क्या करते हैं?</h2><div class="profile-accent"></div><p>इस पहल का परिचय, इसका उद्देश्य और लोगों के लिए इसका व्यावहारिक उपयोग।</p></div>
  <div class="profile-narrative"><p>{{ $project['story'][0] }}</p><div class="profile-inline-note"><span aria-hidden="true">✦</span><strong>{{ $project['headline'] }}</strong></div></div>
</div></section>

<section class="profile-offerings"><div class="container">
  <div class="profile-section-head"><div><span class="profile-section-label">02 · KEY AREAS</span><h2>मुख्य काम और सुविधाएँ</h2></div><p>इस पहल के प्रमुख क्षेत्र एक नज़र में। उपलब्ध सुविधाओं का अंतिम विवरण संबंधित वेबसाइट पर देखें।</p></div>
  <div class="profile-offer-grid">
    @foreach($project['services'] as $service)
    <article class="profile-offer"><span class="profile-offer-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div class="profile-offer-symbol" aria-hidden="true">{{ ['✦', '◈', '◎'][$loop->index % 3] }}</div><h3>{{ $service }}</h3></article>
    @endforeach
  </div>
</div></section>

<section class="profile-story"><div class="container profile-story-grid">
  <div class="profile-story-visual" aria-hidden="true"><div class="profile-visual-orbit"></div><div class="profile-visual-card"><span class="profile-visual-label">{{ $project['category'] }}</span><strong>{{ $project['name'] }}</strong><span class="profile-visual-rule"></span><span>Learn · Connect · Grow</span></div><div class="profile-visual-badge">MCI</div></div>
  <div class="profile-story-copy"><span class="profile-section-label">03 · THE EXPERIENCE</span><h2>यह कैसे काम करता है?</h2><p>{{ $project['story'][1] }}</p><h3>किसके लिए उपयोगी है?</h3><p>{{ $project['story'][2] }}</p></div>
</div></section>

<section class="profile-journey"><div class="container"><div class="profile-section-head"><div><span class="profile-section-label">04 · YOUR NEXT STEPS</span><h2>आगे कैसे बढ़ें</h2></div><p>शुरुआत से उपयोग तक की एक सरल रूपरेखा।</p></div><div class="profile-step-grid">
  @foreach($project['journey'] as $step)
  <div class="profile-step"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><p>{{ $step }}</p></div>
  @endforeach
</div></div></section>

<section class="profile-contact"><div class="container"><div class="profile-contact-card"><div><span class="profile-section-label">CONNECT WITH US</span><h2>{{ $isClientProject ? 'इस कार्यक्रम के बारे में' : 'MCI Educational Group से जुड़ें' }}</h2><p>{{ $isClientProject ? 'यह Aryans News द्वारा आयोजित कार्यक्रम के लिए C-Net Web Services का ग्राहक प्रोजेक्ट है। कार्यक्रम के नियम और आयोजन संबंधी सूचना आधिकारिक इवेंट वेबसाइट पर देखें।' : 'Run under Chandrashekhar & Narayan Educational Trust. इस पहल के बारे में जानकारी, मार्गदर्शन या सही टीम से संपर्क के लिए केंद्रीय कार्यालय से बात करें।' }}</p><p class="profile-address">MCI Campus, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar</p><p><a href="tel:+917004773247">7004773247</a> · <a href="tel:+919334779133">9334779133</a> · <a href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a></p></div><div class="profile-contact-actions">@if($project['url'])<a class="btn btn-light btn-lg fw-bold" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer">{{ $isClientProject ? 'इवेंट वेबसाइट देखें' : 'वेबसाइट देखें' }} ↗</a>@endif<a class="btn btn-outline-light btn-lg fw-bold" href="{{ route('about') }}#our-network">सभी संस्थान देखें</a></div></div></div></section>
@endsection
