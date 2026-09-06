$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "===== MCI BIOMETRIC CONNECTOR PREFLIGHT =====" -ForegroundColor Cyan

$Required = @(
    "MIDIris_Auth.dll",
    "MIDIris_Auth_Core.dll",
    "MR014_MIS100V2_Windows_Auth_IPL.dll",
    "iris_engine_v3.dll",
    "iris_image_record.dll"
)

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$Runtime = Join-Path $Root "runtime"

foreach($Name in $Required) {
    $Path = Join-Path $Runtime $Name

    if(!(Test-Path $Path)) {
        Write-Host "MISSING: $Name" -ForegroundColor Red
    }
    else {
        Write-Host "PASS: $Name" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Architecture required: x86"
Write-Host ".NET Framework: 4.8"
Write-Host "Device: Mantra MIS100V2"
Write-Host "Central mode: Institution / Branch registered by server"
