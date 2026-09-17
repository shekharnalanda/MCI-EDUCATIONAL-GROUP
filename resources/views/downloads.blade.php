@extends('layouts.app')

@section('title', 'Downloads | MCI Educational Group')

@section('content')
<section class="v2-page-hero"><div class="container"><div class="v2-kicker">Resources &amp; Documents</div><h1>Downloads for students, institutions and visitors.</h1><p>Access important forms, brochures, notices and learning resources published by MCI Educational Group.</p></div></section>
<div class="v2-breadcrumb"><div class="container"><a href="{{ route('home') }}">Home</a><span>/</span><span>Downloads</span></div></div>
<section class="v2-section v2-soft">
    <div class="container">
        @php
            $clientAgreementForms = [
                ['C-Net Web Services', '01-C-Net-Web-Services-English-Client-Agreement.pdf'],
                ['C-Net Store', '02-C-Net-Store-English-Client-Agreement.pdf'],
                ['C-Net Computer Education', '03-C-Net-Computer-Education-English-Client-Agreement.pdf'],
                ['C-Net Pathshala', '04-C-Net-Pathshala-English-Client-Agreement.pdf'],
                ['C-Net Library', '05-C-Net-Library-English-Client-Agreement.pdf'],
                ['C-Net AI Studio', '06-C-Net-AI-Studio-English-Client-Agreement.pdf'],
                ['C-Net Meet', '07-C-Net-Meet-English-Client-Agreement.pdf'],
                ['C-Net Social Media', '08-C-Net-Social-Media-English-Client-Agreement.pdf'],
                ['C-Net Vyapar', '09-C-Net-Vyapar-English-Client-Agreement.pdf'],
                ['C-Net PagarBOOK', '10-C-Net-PagarBOOK-English-Client-Agreement.pdf'],
                ['C-Net AI Work', '11-C-Net-AI-Work-English-Client-Agreement.pdf'],
                ['Micro Computer Institute', '12-Micro-Computer-Institute-English-Client-Agreement.pdf'],
                ['MCI Test Series', '13-MCI-Test-Series-English-Client-Agreement.pdf'],
                ['Kushal Youth Program (KYP)', '14-Kushal-Youth-Program-KYP-English-Client-Agreement.pdf'],
                ['MCI Search Engine', '15-MCI-Search-Engine-English-Client-Agreement.pdf'],
                ['MCI Promotion Portal', '16-MCI-Promotion-Portal-English-Client-Agreement.pdf'],
                ['Salary Book', '17-Salary-Book-English-Client-Agreement.pdf'],
                ['Book My Event', '18-Book-My-Event-English-Client-Agreement.pdf'],
            ];
        @endphp
        <div id="client-forms" class="mb-5" style="scroll-margin-top:110px">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div><div class="v2-section-kicker">Official Forms</div><h2 class="v2-title mt-2 mb-1">Client Agreement Forms</h2><p class="v2-copy mb-0">Select the required service and download its one-page agreement PDF.</p></div>
            </div>
            <div class="row g-3">
                @foreach($clientAgreementForms as [$formName, $formFile])
                    <div class="col-sm-6 col-lg-4">
                        <div class="v2-card p-3 d-flex flex-row align-items-center justify-content-between gap-3">
                            <div><div class="small text-uppercase text-secondary fw-semibold">Agreement Form</div><h3 class="h6 fw-bold mb-0 mt-1">{{ $formName }}</h3></div>
                            <a class="btn btn-primary btn-sm flex-shrink-0" href="{{ asset('forms/'.$formFile) }}" download aria-label="Download {{ $formName }} agreement form">Download</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if($items->isNotEmpty())
            <hr class="my-5">
            <div class="mb-4"><div class="v2-section-kicker">Other Resources</div><h2 class="v2-title mt-2">Published Downloads</h2></div>
        <div class="row g-4">
            @foreach($items as $item)
                <div class="col-md-6">
                    <div class="v2-card"><div class="d-flex flex-column h-100">
                            <h2 class="h5 fw-bold">{{ $item->title }}</h2>
                            @if($item->description)<p class="text-secondary flex-grow-1">{{ $item->description }}</p>@endif
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                @if($item->file_path)
                                    <a class="btn btn-primary" href="{{ asset($item->file_path) }}" target="_blank" rel="noopener">Download File</a>
                                @endif
                                @if($item->external_url)
                                    <a class="btn btn-outline-primary" href="{{ $item->external_url }}" target="_blank" rel="noopener">Open Link</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
