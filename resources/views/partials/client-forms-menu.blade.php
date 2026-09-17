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
<li class="nav-item dropdown forms-dropdown">
    <a class="nav-link dropdown-toggle forms-trigger" href="{{ route('downloads') }}#client-forms" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        📄 Download Forms
    </a>
    <div class="dropdown-menu dropdown-menu-end forms-menu p-0">
        <div class="forms-menu-head">
            <strong>Client Agreement Forms</strong>
            <small>Choose a service to download its one-page PDF</small>
        </div>
        <div class="forms-menu-list">
            @foreach($clientAgreementForms as [$formName, $formFile])
                <a class="dropdown-item forms-menu-item" href="{{ asset('forms/'.$formFile) }}" download>
                    <span>{{ $formName }}</span><b aria-hidden="true">↓</b>
                </a>
            @endforeach
        </div>
        <a class="forms-menu-all" href="{{ route('downloads') }}#client-forms">View all forms</a>
    </div>
</li>
