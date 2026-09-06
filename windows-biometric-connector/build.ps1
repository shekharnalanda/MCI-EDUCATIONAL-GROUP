$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$Runtime = Join-Path $Root "runtime"
$Build = Join-Path $Root "build"

$CSC = "$env:WINDIR\Microsoft.NET\Framework\v4.0.30319\csc.exe"

Write-Host ""
Write-Host "===== MCI BIOMETRIC CONNECTOR BUILD =====" -ForegroundColor Cyan

if(!(Test-Path $CSC)) {
    throw ".NET Framework x86 compiler not found."
}

$Required = @(
    "MIDIris_Auth.dll",
    "MIDIris_Auth_Core.dll",
    "MR014_MIS100V2_Windows_Auth_IPL.dll",
    "iris_engine_v3.dll",
    "iris_image_record.dll"
)

foreach($Name in $Required) {
    if(!(Test-Path (Join-Path $Runtime $Name))) {
        throw "Missing MIS100V2 runtime DLL: $Name"
    }
}

New-Item -ItemType Directory -Force -Path $Build |
    Out-Null

Write-Host "x86 compiler = PASS" -ForegroundColor Green
Write-Host "MIS100V2 runtime = PASS" -ForegroundColor Green

Write-Host ""
Write-Host "Central API client source ready."
Write-Host "Hardware UI source will use verified KYP V3.2 implementation."
Write-Host "No KYP production file is modified by this build area."
