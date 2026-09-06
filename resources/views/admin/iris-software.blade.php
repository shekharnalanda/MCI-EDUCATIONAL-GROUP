@extends('admin.layouts.app')

@section('content')

<style>
.iris-wrap{max-width:1180px;margin:0 auto}
.iris-hero{
    padding:28px;
    border-radius:18px;
    background:linear-gradient(135deg,#073f7b,#0867b8);
    color:#fff;
    margin-bottom:24px
}
.iris-hero h1{margin:0 0 8px;font-size:28px}
.iris-hero p{margin:0;opacity:.92;max-width:780px}
.iris-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:18px
}
.iris-card{
    background:#fff;
    border:1px solid #dce5ef;
    border-radius:16px;
    padding:22px;
    box-shadow:0 6px 22px rgba(25,55,90,.07)
}
.iris-step{
    width:38px;height:38px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:#eaf3ff;color:#0755a5;
    font-weight:800;margin-bottom:16px
}
.iris-card h3{margin:0 0 9px;color:#17345b}
.iris-card p{color:#64748b;min-height:68px}
.iris-meta{font-size:13px;color:#64748b;margin:14px 0}
.iris-btn{
    display:block;text-align:center;
    padding:11px 14px;border-radius:9px;
    background:#0755a5;color:#fff!important;
    text-decoration:none;font-weight:700
}
.iris-btn.green{background:#138a63}
.iris-disabled{
    display:block;text-align:center;
    padding:11px 14px;border-radius:9px;
    background:#edf1f5;color:#7a8795;
    font-weight:700
}
.iris-note{
    margin-top:22px;padding:18px;
    border-radius:12px;background:#fff8e6;
    border:1px solid #f3d58a;color:#6b5315
}
.iris-security{
    margin-top:18px;padding:18px;
    border-radius:12px;background:#edf8f3;
    color:#245a45
}
@media(max-width:900px){
    .iris-grid{grid-template-columns:1fr}
}
</style>

<div class="iris-wrap">

    <div class="iris-hero">
        <h1>Iris Attendance Software</h1>
        <p>
            Official Windows software and supporting packages for
            MCI Educational Group Central Biometric Attendance using
            Mantra MIS100V2.
        </p>
    </div>

    <div class="iris-grid">

        <div class="iris-card">
            <div class="iris-step">1</div>
            <h3>Mantra MIS100V2 Driver</h3>
            <p>
                Install the official device driver before connecting
                the MIS100V2 iris scanner.
            </p>

            <div class="iris-meta">
                Windows • Mantra MIS100V2
            </div>

            @if($driverAvailable)
                <a class="iris-btn"
                   href="{{ route('admin.iris-software.download','driver') }}">
                    Download Driver
                </a>
            @else
                <span class="iris-disabled">
                    Driver Package Pending
                </span>
            @endif
        </div>

        <div class="iris-card">
            <div class="iris-step">2</div>
            <h3>Windows Runtime</h3>
            <p>
                Required MIS100V2 support runtime and prerequisites
                for the MCI Central Biometric Connector.
            </p>

            <div class="iris-meta">
                Windows x86 • MIS100V2 Runtime
            </div>

            @if($runtimeAvailable)
                <a class="iris-btn"
                   href="{{ route('admin.iris-software.download','runtime') }}">
                    Download Runtime
                </a>
            @else
                <span class="iris-disabled">
                    Runtime Package Pending
                </span>
            @endif
        </div>

        <div class="iris-card">
            <div class="iris-step">3</div>
            <h3>MCI Biometric Connector</h3>
            <p>
                Central multi-institution iris enrollment,
                live preview, identification and Check-In/Check-Out.
            </p>

            <div class="iris-meta">
                Windows x86
                @if($connectorSize)
                    • {{ $connectorSize }}
                @endif
            </div>

            @if($connectorAvailable)
                <a class="iris-btn green"
                   href="{{ route('admin.iris-software.download','connector') }}">
                    Download MCI Connector
                </a>
            @else
                <span class="iris-disabled">
                    Connector Package Unavailable
                </span>
            @endif
        </div>

    </div>

    <div class="iris-note">
        <strong>Installation order:</strong>
        MIS100V2 Driver → Windows Runtime → MCI Biometric Connector.
        Each attendance computer must use the Device Code and Device
        Token issued for its own institution/branch.
    </div>

    <div class="iris-security">
        <strong>Security:</strong>
        Device Tokens are not included in downloadable packages.
        Iris software downloads are provided through authenticated
        Central Admin routes.
    </div>

</div>

@endsection
