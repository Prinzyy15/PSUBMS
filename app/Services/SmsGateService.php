<?php
namespace App\Services;

use App\Services\Contracts\SmsGatewayInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Jobs\SendSmsJob;

class SmsGateService implements SmsGatewayInterface
{
    protected $endpoint;
    protected $username;
    protected $password;
    protected $timeout;
    protected $retries;

    public function __construct()
    {
        $this->endpoint = Config::get('smsgate.endpoint');
        $this->username = Config::get('smsgate.username');
        $this->password = Config::get('smsgate.password');
        $this->timeout = Config::get('smsgate.timeout', 10);
        $this->retries = Config::get('smsgate.retries', 2);
    }

    public function send($phoneNumbers, string $message, array $meta = []): array
    {
        if (!Config::get('smsgate.enabled')) {
            Log::info('SmsGateService::send - disabled by config', ['phoneNumbers' => $phoneNumbers]);
            return ['success' => false, 'id' => null, 'response' => 'disabled'];
        }

        if (Config::get('smsgate.dry_run')) {
            Log::info('SmsGateService::send - dry run', ['phoneNumbers' => $phoneNumbers, 'message' => $message, 'meta' => $meta]);
            return ['success' => true, 'id' => 'dry-run', 'response' => null];
        }

        $numbers = is_array($phoneNumbers) ? $phoneNumbers : [$phoneNumbers];
        $normalized = [];

        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            foreach ($numbers as $n) {
                try {
                    $p = $phoneUtil->parse($n, null);
                    if ($phoneUtil->isValidNumber($p)) {
                        $normalized[] = $phoneUtil->format($p, PhoneNumberFormat::E164);
                    } else {
                        Log::warning('SmsGateService::send - invalid phone number', ['number' => $n]);
                    }
                } catch (NumberParseException $e) {
                    Log::warning('SmsGateService::send - number parse failed', ['number' => $n, 'error' => $e->getMessage()]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('SmsGateService::send - libphonenumber not available, using raw numbers', ['error' => $e->getMessage()]);
            $normalized = $numbers;
        }

        if (empty($normalized)) {
            return ['success' => false, 'id' => null, 'response' => 'no valid phone numbers'];
        }

        if (Config::get('smsgate.use_queue')) {
            dispatch(new SendSmsJob($normalized, $message, $meta));
            return ['success' => true, 'id' => null, 'response' => 'queued'];
        }

        $client = new Client(['timeout' => $this->timeout]);
        $payload = ['message' => $message, 'phoneNumbers' => $normalized];

        $attempt = 0;
        while ($attempt <= $this->retries) {
            try {
                $attempt++;
                $resp = $client->post($this->endpoint, [
                    'auth' => [$this->username, $this->password],
                    'json' => $payload,
                    'headers' => ['Content-Type' => 'application/json'],
                ]);

                $body = json_decode((string)$resp->getBody(), true);
                Log::info('SmsGateService::send - sent', ['response' => $body]);
                return ['success' => true, 'id' => $body['messageId'] ?? null, 'response' => $body];
            } catch (RequestException $e) {
                Log::error('SmsGateService::send - request exception', ['attempt' => $attempt, 'error' => $e->getMessage()]);
                if ($attempt >= $this->retries) {
                    return ['success' => false, 'id' => null, 'response' => $e->getMessage()];
                }
                sleep(1 * $attempt);
            }
        }

        return ['success' => false, 'id' => null, 'response' => 'unknown error'];
    }
}


