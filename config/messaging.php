<?php

return [
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'graph_url' => env('WHATSAPP_GRAPH_URL', 'https://graph.facebook.com'),
        'graph_version' => env('WHATSAPP_GRAPH_VERSION'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'template_name' => env('WHATSAPP_DEFAULT_TEMPLATE'),
        'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en'),
    ],

    'sms' => [
        'enabled' => env('SMS_ENABLED', false),
        'driver' => env('SMS_DRIVER'),
        'endpoint' => env('SMS_ENDPOINT'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
        'entity_id' => env('SMS_DLT_ENTITY_ID'),
        'template_id' => env('SMS_DLT_TEMPLATE_ID'),
    ],
];
