using System;
using System.IO;
using System.Net;
using System.Text;
using System.Web.Script.Serialization;

namespace MCIBiometricConnector
{
    public sealed class CentralApiClient
    {
        private readonly string serverUrl;
        private readonly string deviceToken;
        private readonly JavaScriptSerializer json =
            new JavaScriptSerializer();

        public CentralApiClient(
            string serverUrl,
            string deviceToken
        )
        {
            this.serverUrl =
                (serverUrl ?? "").TrimEnd('/');

            this.deviceToken =
                deviceToken ?? "";
        }

        private string Request(
            string method,
            string path,
            object body = null
        )
        {
            var request = (HttpWebRequest)
                WebRequest.Create(serverUrl + path);

            request.Method = method;
            request.Accept = "application/json";
            request.ContentType = "application/json";
            request.Headers["X-Device-Token"] =
                deviceToken;

            request.Timeout = 30000;
            request.ReadWriteTimeout = 30000;

            if (body != null)
            {
                byte[] payload = Encoding.UTF8.GetBytes(
                    json.Serialize(body)
                );

                request.ContentLength = payload.Length;

                using (
                    Stream stream =
                        request.GetRequestStream()
                )
                {
                    stream.Write(
                        payload,
                        0,
                        payload.Length
                    );
                }
            }

            try
            {
                using (
                    var response =
                        (HttpWebResponse)
                        request.GetResponse()
                )
                using (
                    var reader =
                        new StreamReader(
                            response.GetResponseStream()
                        )
                )
                {
                    return reader.ReadToEnd();
                }
            }
            catch (WebException ex)
            {
                string detail = ex.Message;

                if (ex.Response != null)
                {
                    using (
                        var reader =
                            new StreamReader(
                                ex.Response
                                    .GetResponseStream()
                            )
                    )
                    {
                        detail = reader.ReadToEnd();
                    }
                }

                throw new Exception(
                    "Central API error: " + detail,
                    ex
                );
            }
        }

        public string Heartbeat(
            string agentVersion
        )
        {
            return Request(
                "POST",
                "/api/v1/iris/heartbeat",
                new {
                    agent_version = agentVersion,
                    device_model = "Mantra MIS100V2"
                }
            );
        }

        public string Roster()
        {
            return Request(
                "GET",
                "/api/v1/iris/roster"
            );
        }

        public string Enroll(
            int attendanceStudentId,
            string eye,
            string templateBase64,
            int qualityScore
        )
        {
            return Request(
                "POST",
                "/api/v1/iris/enrollments",
                new {
                    attendance_student_id =
                        attendanceStudentId,
                    eye = eye,
                    template = templateBase64,
                    quality_score = qualityScore
                }
            );
        }

        public string MarkAttendance(
            int attendanceStudentId,
            string eventUuid,
            string eventType,
            DateTime capturedAt,
            int matchScore,
            int qualityScore,
            string sessionKey
        )
        {
            return Request(
                "POST",
                "/api/v1/iris/attendance",
                new {
                    attendance_student_id =
                        attendanceStudentId,
                    event_uuid = eventUuid,
                    event_type = eventType,
                    captured_at =
                        capturedAt.ToString("o"),
                    match_score = matchScore,
                    quality_score = qualityScore,
                    session_key = sessionKey,
                    agent_version =
                        "MCI-BIOMETRIC-1.0"
                }
            );
        }
    }
}
