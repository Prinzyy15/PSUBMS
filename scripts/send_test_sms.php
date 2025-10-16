<?php
// One-off script to synchronously call SmsGateService::send()
// Usage: php scripts/send_test_sms.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Bootstrapped application\n";

try {
    // Show config values for verification
    $endpoint = config('smsgate.endpoint');
    $username = config('smsgate.username');
    $enabled = config('smsgate.enabled') ? 'true' : 'false';
    $dry = config('smsgate.dry_run') ? 'true' : 'false';

    echo "smsgate.endpoint={$endpoint}\n";
    echo "smsgate.username={$username}\n";
    echo "smsgate.enabled={$enabled}\n";
    echo "smsgate.dry_run={$dry}\n";

    $svc = new \App\Services\SmsGateService();

    $target = '+639271992795';
    $message = "[TEST] Monthly report SMS (synchronous) at " . date('c');

    echo "Sending to {$target}...\n";
    $result = $svc->send($target, $message, ['test' => true]);

    echo "RESULT:\n" . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e;
}

echo "Done.\n";
