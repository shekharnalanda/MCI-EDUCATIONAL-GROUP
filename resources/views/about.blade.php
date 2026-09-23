@extends('layouts.app')

@section('title', 'About Us | MCI Educational Group')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/about-group.css') }}">
@endpush

@section('content')
@php
$bySlug = collect($projects)->keyBy('slug');
$wings = [
    ['id' => 'education', 'number' => '01', 'label' => 'Education & learning', 'title' => 'शिक्षा और कौशल विकास', 'intro' => 'कक्षा में सीखना, कंप्यूटर पर अभ्यास करना, परीक्षा की तैयारी और पढ़ने के लिए सही माहौल—इन जरूरतों के लिए हमारे अलग-अलग शिक्षण केंद्र और डिजिटल मंच काम करते हैं।', 'slugs' => ['micro-computer-institute', 'c-net-computer-education', 'c-net-pathshala', 'kushal-youth-program', 'c-net-library', 'mci-test-series']],
    ['id' => 'digital', 'number' => '02', 'label' => 'Technology & digital services', 'title' => 'डिजिटल मंच और तकनीकी सेवाएँ', 'intro' => 'स्थानीय व्यवसाय की ऑनलाइन पहचान से लेकर वेबसाइट, AI आधारित रचनात्मक काम और ऑनलाइन संवाद तक, ये पहलें लोगों व संस्थानों को तकनीक का व्यावहारिक उपयोग करने में मदद करती हैं।', 'slugs' => ['c-net-web-services', 'mci-search-engine', 'c-net-ai-studio', 'c-net-meet']],
    ['id' => 'business', 'number' => '03', 'label' => 'Business & commerce', 'title' => 'व्यवसाय, वाणिज्य और प्रबंधन', 'intro' => 'दुकानों, छोटे व्यवसायों और आयोजकों के रोजमर्रा के काम के लिए डिजिटल बिक्री, लेखा, उपस्थिति, वेतन और आयोजन प्रबंधन के अलग समाधान विकसित किए जा रहे हैं।', 'slugs' => ['c-net-store', 'c-net-vyapar', 'c-net-pagarbook', 'salary-book', 'book-my-event']],
    ['id' => 'media', 'number' => '04', 'label' => 'Media & community', 'title' => 'मीडिया, संवाद और समुदाय', 'intro' => 'सूचना, सीखने की सामग्री और लोगों के बीच संवाद के लिए समाचार, नेटवर्क और समुदाय से जुड़े डिजिटल स्थान इस समूह की व्यापक पहल का हिस्सा हैं।', 'slugs' => ['c-net-news', 'mci-c-net-network', 'c-net-social-media', 'shekhar-family-portal']],
];
@endphp

<section class="group-hero">
  <div class="container group-hero-inner">
    <div>
      <div class="group-eyebrow">ABOUT US · MCI EDUCATIONAL GROUP</div>
      <h1>शिक्षा से डिजिटल अवसर तक, <em>एक साझा दृष्टि।</em></h1>
      <p>MCI Educational Group शिक्षा, कौशल, तकनीक और सेवा से जुड़ी पहलों का साझा परिचय है। प्रत्येक पहल का अपना काम और अपना मंच है; यहाँ आप समझ सकते हैं कि वे किसके लिए हैं और कैसे उपयोगी हैं।</p>
      <div class="group-trust-line">संचालित: <strong>Chandrashekhar &amp; Narayan Educational Trust</strong></div>
      <a class="group-primary-link" href="#our-work">हमारे कार्यक्षेत्र देखें <span aria-hidden="true">↓</span></a>
    </div>
    <div class="group-hero-art" aria-hidden="true">
      <div class="group-art-orbit group-art-orbit-one"></div><div class="group-art-orbit group-art-orbit-two"></div>
      <div class="group-art-center">MCI <small>Educational Group</small></div>
      <div class="group-art-chip group-art-chip-a">शिक्षा <span>Education</span></div>
      <div class="group-art-chip group-art-chip-b">कौशल <span>Skills</span></div>
      <div class="group-art-chip group-art-chip-c">तकनीक <span>Technology</span></div>
      <div class="group-art-chip group-art-chip-d">समुदाय <span>Community</span></div>
    </div>
  </div>
</section>

<nav class="group-breadcrumb" aria-label="Breadcrumb"><div class="container"><a href="{{ route('home') }}">Home</a><span>/</span><span>About MCI Educational Group</span></div></nav>

<div class="group-jump-wrap"><nav class="container group-jump" aria-label="About sections"><a href="#who-we-are">हमारी पहचान</a><a href="#trust">Trust</a><a href="#our-work">हमारे कार्यक्षेत्र</a><a href="#our-network">सभी पहलें</a><a href="#contact">संपर्क</a></nav></div>

<section class="group-section" id="who-we-are"><div class="container group-intro-grid">
  <div><span class="group-kicker">WHO WE ARE</span><h2>हम कौन हैं और<br>क्या करते हैं?</h2><p class="group-lead">एक समूह, जिसमें सीखने से लेकर अपने काम को डिजिटल रूप देने तक कई ज़रूरतों के लिए अलग-अलग पहलें जुड़ी हैं।</p></div>
  <div class="group-prose"><p>MCI Educational Group के अंतर्गत शिक्षा, प्रशिक्षण, परीक्षा अभ्यास, तकनीकी सेवाएँ, व्यापारिक सॉफ्टवेयर, स्थानीय वाणिज्य और मीडिया से संबंधित मंच प्रस्तुत किए जाते हैं। हमारी कोशिश है कि विद्यार्थी को केवल पाठ्यक्रम की जानकारी नहीं, बल्कि अभ्यास और आगे बढ़ने का रास्ता भी मिले; व्यवसाय को केवल ऑनलाइन नाम नहीं, बल्कि कामकाज के लिए उपयोगी डिजिटल साधन मिलें।</p><p>इसी कारण समूह की हर इकाई का उद्देश्य अलग रखा गया है। कंप्यूटर संस्थान में प्रशिक्षक और लैब के साथ सीखने का अनुभव मिलता है; Test Series परीक्षा की तैयारी में अभ्यास और परिणाम की समीक्षा पर केंद्रित है; Web Services डिजिटल उपस्थिति बनाने में मदद करती है। नीचे हर पहल का परिचय और उसके विस्तृत पेज का रास्ता दिया गया है।</p><p>कुछ मंच विकसित हो रहे हैं या उनकी सुविधाएँ समय के साथ बदल सकती हैं। प्रवेश, सेवा की उपलब्धता, शुल्क और वर्तमान सुविधाओं की जानकारी संबंधित आधिकारिक वेबसाइट से जाँचें।</p></div>
</div></section>

<section class="group-section group-trust-section" id="trust"><div class="container group-trust-grid">
  <div><span class="group-kicker group-kicker-light">INSTITUTIONAL FOUNDATION</span><h2>हमारा आधार:<br>Chandrashekhar &amp; Narayan<br>Educational Trust</h2></div>
  <div><p><strong>MCI Educational Group, Chandrashekhar &amp; Narayan Educational Trust के अंतर्गत संचालित है।</strong> यह पहचान समूह की शैक्षिक और सेवा संबंधी पहलों को एक साझा संस्थागत आधार देती है। समूह की वेबसाइट पर उनके काम और संपर्क को एक स्थान पर समझा जा सकता है।</p><p>शिक्षा से जुड़ी पहलें विद्यार्थियों, परिवारों और शिक्षकों की जरूरतों से शुरू होती हैं। तकनीकी और व्यवसायिक पहलें उसी व्यावहारिक सोच को डिजिटल दुनिया तक ले जाती हैं। किसी खास इकाई की सेवाएँ, संचालन व्यवस्था और ताज़ा जानकारी जानने के लिए उसका अपना परिचय पेज और आधिकारिक पोर्टल देखें।</p></div>
</div></section>

<section class="group-section group-overview" id="our-work"><div class="container"><span class="group-kicker">WHAT WE DO</span><h2>हमारे कार्यक्षेत्र</h2><p class="group-section-desc">समूह की पहलें चार प्रमुख क्षेत्रों में समझी जा सकती हैं। नीचे प्रत्येक क्षेत्र में किए जाने वाले काम और उससे जुड़ी इकाइयों का विस्तार है।</p><div class="group-overview-grid">
@foreach($wings as $wing)
<a href="#{{ $wing['id'] }}"><span class="group-overview-number">{{ $wing['number'] }}</span><strong>{{ $wing['title'] }}</strong><span>{{ $wing['intro'] }}</span><b aria-hidden="true">↘</b></a>
@endforeach
</div></div></section>

<div id="our-network">
@foreach($wings as $wing)
<section class="group-section group-wing {{ $loop->even ? 'group-wing-alt' : '' }}" id="{{ $wing['id'] }}"><div class="container">
  <div class="group-wing-heading"><div><span class="group-kicker">{{ $wing['number'] }} · {{ $wing['label'] }}</span><h2>{{ $wing['title'] }}</h2></div><p>{{ $wing['intro'] }}</p></div>
  <div class="group-project-grid">
  @foreach($wing['slugs'] as $slug)
    @php $project = $bySlug->get($slug); @endphp
    @if($project)
    <article class="group-project"><div class="group-project-top"><span class="group-project-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="group-project-category">{{ $project['category'] }}</span></div><h3>{{ $project['name'] }}</h3><p class="group-project-headline">{{ $project['headline'] }}</p><p>{{ $project['story'][0] }}</p><p>{{ $project['story'][1] }}</p><a href="{{ route('about.project', $project['slug']) }}">पूरा परिचय पढ़ें <span aria-hidden="true">↗</span></a></article>
    @endif
  @endforeach
  </div>
</div></section>
@endforeach
</div>

<section class="group-section group-client"><div class="container group-client-grid"><div><span class="group-kicker">SPECIAL PROJECT</span><h2>सहयोगी परियोजना</h2><p>समूह की सेवाओं के अलावा, हमारी टीम अन्य आयोजकों के लिए भी डिजिटल मंच तैयार करती है। ऐसी परियोजनाओं में आयोजक और तकनीकी सेवा प्रदाता की भूमिकाएँ स्पष्ट रखी जाती हैं।</p></div>@php $client = $bySlug->get('aryans-dandiya-night'); @endphp @if($client)<article class="group-project"><div class="group-project-top"><span class="group-project-index">↗</span><span class="group-project-category">Client project</span></div><h3>{{ $client['name'] }}</h3><p>{{ $client['story'][0] }}</p><p><strong>आयोजक: Aryans News.</strong> C-Net Web Services ने आयोजन के लिए डिजिटल प्लेटफॉर्म विकसित किया है।</p><a href="{{ route('about.project', $client['slug']) }}">परियोजना का परिचय <span aria-hidden="true">↗</span></a></article>@endif</div></section>

<section class="group-section group-contact" id="contact"><div class="container group-contact-grid"><div><span class="group-kicker">CONNECT WITH US</span><h2>सही पहल से जुड़ें</h2><p>किसी संस्थान में प्रवेश, प्रशिक्षण या डिजिटल सेवा के बारे में जानना हो तो उसके परिचय पेज से आधिकारिक वेबसाइट खोलें। समूह संबंधी सामान्य जानकारी के लिए केंद्रीय संपर्क पर बात करें।</p></div><div class="group-contact-card"><strong>MCI Educational Group</strong><span>Run under Chandrashekhar &amp; Narayan Educational Trust</span><address>MCI Campus, Quamruddin Ganj, Bihar Sharif, Nalanda – 803101, Bihar</address><a href="tel:+917004773247">7004773247</a><a href="tel:+919334779133">9334779133</a><a href="mailto:mcieducationalgroup@gmail.com">mcieducationalgroup@gmail.com</a></div></div></section>
@endsection
