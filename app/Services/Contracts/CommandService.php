<?php

namespace App\Services\Contracts;

interface CommandService
{
    /**
     * @param int $userId
     * @param string $code
     * @param string|null $from
     * @param string|null $to
     */
    public function startProcessingCommand(int $userId, string $code, string $from = null, string $to = null): void;
}
