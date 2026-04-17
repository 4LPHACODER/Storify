<?php

namespace App\Contracts;

interface SmsSenderContract
{
    /**
     * Send an SMS via the configured provider.
     *
     * @throws \Throwable When the send operation fails and should be surfaced to the job layer.
     */
    public function send(string $phoneNumber, string $message): void;
}
