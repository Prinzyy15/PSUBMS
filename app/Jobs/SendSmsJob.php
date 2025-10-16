<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SmsGateService;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $numbers;
    public $message;
    public $meta;

    public function __construct(array $numbers, string $message, array $meta = [])
    {
        $this->numbers = $numbers;
        $this->message = $message;
        $this->meta = $meta;
    }

    public function handle()
    {
        try {
            $svc = new SmsGateService();
            $result = $svc->send($this->numbers, $this->message, $this->meta);
            Log::info('SendSmsJob handled', ['result' => $result]);
        } catch (\Throwable $e) {
            Log::error('SendSmsJob failed', ['error' => $e->getMessage()]);
        }
    }
}
