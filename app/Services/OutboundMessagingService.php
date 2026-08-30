<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OutboundMessagingService
{
    public function status(): array
    {
        return [
            'whatsapp' => [
                'enabled' => (bool) config('messaging.whatsapp.enabled'),
                'ready' => $this->whatsappReady(),
            ],
            'sms' => [
                'enabled' => (bool) config('messaging.sms.enabled'),
                'ready' => $this->smsReady(),
            ],
        ];
    }

    public function whatsappReady(): bool
    {
        return (bool) config('messaging.whatsapp.enabled')
            && filled(config('messaging.whatsapp.graph_version'))
            && filled(config('messaging.whatsapp.phone_number_id'))
            && filled(config('messaging.whatsapp.access_token'))
            && filled(config('messaging.whatsapp.template_name'));
    }

    public function smsReady(): bool
    {
        return (bool) config('messaging.sms.enabled')
            && config('messaging.sms.driver') === 'generic_json'
            && filled(config('messaging.sms.endpoint'))
            && filled(config('messaging.sms.api_key'))
            && filled(config('messaging.sms.sender_id'))
            && filled(config('messaging.sms.entity_id'))
            && filled(config('messaging.sms.template_id'));
    }

    public function sendWhatsAppTemplate(string $phone, string $message): array
    {
        if (!$this->whatsappReady()) {
            throw new RuntimeException('WhatsApp channel is not configured or enabled.');
        }

        $phone = $this->normalizeIndianPhone($phone);
        $base = rtrim((string) config('messaging.whatsapp.graph_url'), '/');
        $version = trim((string) config('messaging.whatsapp.graph_version'), '/');
        $phoneNumberId = config('messaging.whatsapp.phone_number_id');

        $response = Http::withToken((string) config('messaging.whatsapp.access_token'))
            ->acceptJson()
            ->timeout(15)
            ->post("{$base}/{$version}/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => config('messaging.whatsapp.template_name'),
                    'language' => ['code' => config('messaging.whatsapp.template_language', 'en')],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => [[
                            'type' => 'text',
                            'text' => mb_substr($message, 0, 900),
                        ]],
                    ]],
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('WhatsApp provider rejected message: '.$response->status().' '.mb_substr($response->body(), 0, 500));
        }

        $json = $response->json();
        return [
            'provider_reference' => data_get($json, 'messages.0.id'),
            'response' => $json,
        ];
    }

    public function sendSms(string $phone, string $message): array
    {
        if (!$this->smsReady()) {
            throw new RuntimeException('SMS channel is not configured, DLT-ready, or enabled.');
        }

        $phone = $this->normalizeIndianPhone($phone);
        $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('messaging.sms.api_key'),
                'Accept' => 'application/json',
            ])
            ->timeout(15)
            ->post((string) config('messaging.sms.endpoint'), [
                'to' => $phone,
                'message' => $message,
                'sender_id' => config('messaging.sms.sender_id'),
                'entity_id' => config('messaging.sms.entity_id'),
                'template_id' => config('messaging.sms.template_id'),
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('SMS provider rejected message: '.$response->status().' '.mb_substr($response->body(), 0, 500));
        }

        $json = $response->json();
        return [
            'provider_reference' => data_get($json, 'message_id') ?? data_get($json, 'id'),
            'response' => $json,
        ];
    }

    private function normalizeIndianPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';
        if (strlen($digits) === 10) {
            return '91'.$digits;
        }
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return $digits;
        }
        throw new RuntimeException('A valid Indian mobile number is required.');
    }
}
