<?php

return [
    // Enable or disable SMS sending. When false, sends are no-ops.
    'enabled' => env('SMSGATE_ENABLED', false),

    // API endpoint for the gateway (cloud default)
    'endpoint' => env('SMSGATE_ENDPOINT', 'https://api.sms-gate.app/3rdparty/v1/message'),

    // Basic auth credentials
    'username' => env('SMSGATE_USERNAME', null),
    'password' => env('SMSGATE_PASSWORD', null),

    // Request timeout (seconds)
    'timeout' => env('SMSGATE_TIMEOUT', 10),

    // Number of retries for transient errors
    'retries' => env('SMSGATE_RETRIES', 2),

    // If true, dispatch sends to the queue instead of sending synchronously
    'use_queue' => env('SMSGATE_USE_QUEUE', true),

    // Dry run - log payloads and return simulated success
    'dry_run' => env('SMSGATE_DRY_RUN', false),
];
