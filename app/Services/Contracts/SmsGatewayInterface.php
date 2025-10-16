<?php
namespace App\Services\Contracts;

interface SmsGatewayInterface
{
    /**
     * Send a message to one or more phone numbers.
     *
     * @param string|array $phoneNumbers
     * @param string $message
     * @param array $meta Optional metadata (student_id, parent_id, etc)
     * @return array ['success' => bool, 'id' => null|string, 'response' => mixed]
     */
    public function send($phoneNumbers, string $message, array $meta = []): array;
}
