# MCI Biometric Connector

Reusable Windows attendance connector for MCI Educational Group.

## Hardware

Mantra MIS100V2 Iris Scanner

## Windows requirements

- Windows 10/11
- .NET Framework 4.8
- x86 build/runtime
- Mantra MIS100V2 SDK runtime DLLs

## Central API

- POST /api/v1/iris/heartbeat
- GET /api/v1/iris/roster
- POST /api/v1/iris/enrollments
- POST /api/v1/iris/attendance

## Attendance

Mark-In:

event_type = check_in

Mark-Out:

event_type = check_out

If Mark-Out is missed, Central Attendance automatically closes
the open record after 120 minutes.

## Multi-Institution operation

The executable does not hard-code KYP or another institution.

Central Admin creates:

Institution → Branch → Device → Device Token

The connector receives only:

- Server URL
- Device Token

Institution and branch scope are determined by the registered device.

This allows the same connector foundation to serve all MCI institutions.
