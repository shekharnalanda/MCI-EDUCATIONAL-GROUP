# MCI Central Biometric Connector

## Purpose

One reusable Windows MIS100V2 connector for all institutions and branches
under MCI Educational Group.

## Configuration

Each installation receives:

- Central Server URL
- Device Token
- Institution identity from server
- Branch identity from registered device
- Device name/code
- MIS100V2 runtime

The connector must not hard-code a specific institution.

## Central workflow

1. Device authenticates with its device token.
2. Heartbeat confirms device/server connectivity.
3. Roster is downloaded from Central Attendance.
4. Iris enrollment is stored against the Central person record.
5. Person can be:
   - student
   - teacher
   - staff
   - administrator
6. Iris Mark-In sends event_type=check_in.
7. Iris Mark-Out sends event_type=check_out.
8. Central server calculates attendance duration.
9. Missing Mark-Out is closed automatically after 120 minutes.
10. Central dashboard monitors all institutions and branches.

## Security

- Iris templates remain server-side encrypted.
- Raw biometric images are not to be persisted by the connector.
- Device token identifies and authorizes the registered attendance computer.
- Institution/branch scope comes from Central device registration.

## Reuse

The same executable is intended for:

- KYP
- Micro Computer Institute
- C-Net Computer Education
- C-Net Pathshala
- future MCI Educational Group institutions

Only device/server configuration changes.
