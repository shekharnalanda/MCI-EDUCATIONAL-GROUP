using System;
using System.IO;
using System.Web.Script.Serialization;

namespace MCIBiometricConnector
{
    public sealed class ConnectorConfig
    {
        public string server_url { get; set; }
        public string device_token { get; set; }
        public string device_name { get; set; }
        public string device_model { get; set; }

        public static ConnectorConfig Load(
            string path
        )
        {
            if (!File.Exists(path))
            {
                throw new Exception(
                    "Configuration file not found: "
                    + path
                );
            }

            string raw =
                File.ReadAllText(path);

            var config =
                new JavaScriptSerializer()
                    .Deserialize<ConnectorConfig>(raw);

            if (
                config == null ||
                String.IsNullOrWhiteSpace(
                    config.server_url
                ) ||
                String.IsNullOrWhiteSpace(
                    config.device_token
                )
            )
            {
                throw new Exception(
                    "Server URL and Device Token are required."
                );
            }

            return config;
        }
    }
}
