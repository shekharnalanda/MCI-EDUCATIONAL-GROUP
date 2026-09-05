# MCI MIS100V2 Iris Attendance API

The Windows attendance agent communicates only over HTTPS with `https://mciedu.in/api/v1/iris`.

## Authentication

Every request must include the one-time credentials generated in **Master Admin → Iris Attendance → MIS100V2 Devices**:

```text
X-MCI-Device-Code: device-code
X-MCI-Device-Token: one-time-secret-token
Accept: application/json
```

The server stores only a SHA-256 hash of the device token. Regenerating a token immediately invalidates the old token.

## Endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| POST | `/heartbeat` | Device health and agent/SDK version |
| GET | `/roster` | Active institution students and encrypted-at-rest iris templates |
| POST | `/enrollments` | Store or replace a student's iris template |
| POST | `/attendance` | Idempotently mark an identified student present |

### Enrollment body

```json
{
  "student_id": 1,
  "eye": "left",
  "template": "BASE64_MIS100V2_TEMPLATE",
  "quality_score": 82.5,
  "sdk_version": "MIS100V2"
}
```

Only biometric templates are accepted. The agent must never upload raw iris photographs.

### Attendance body

```json
{
  "event_uuid": "f74d1684-17c1-4f33-91ed-1d3efbff9130",
  "student_id": 1,
  "captured_at": "2026-09-05T12:15:10+05:30",
  "session_key": "daily",
  "match_score": 91.25,
  "quality_score": 84.0,
  "agent_version": "1.0.0"
}
```

`event_uuid` makes offline retries safe. A second event for the same student, date and `session_key` returns `duplicate: true` instead of creating another attendance record. Institutions with multiple daily sessions can use stable keys such as `cit-01` or `lab-afternoon`.

The success response contains the student's name, photo URL, course/class, institution, attendance date/time and duplicate status for immediate kiosk display.

## Security rules

- One device belongs to exactly one institution.
- A device can access only its institution's students.
- Iris templates use Laravel encrypted casts at rest.
- Raw iris images are not stored.
- Disabled devices and institutions are rejected.
- Captures more than seven days old or over ten minutes in the future are rejected.
- Device credentials must be stored with Windows DPAPI/Credential Manager in the production agent.
