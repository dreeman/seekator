<?php

namespace App\Services\Contracts;

/**
 * Class VideoService
 * @package App\Services
 */
interface VideoService
{
    /**
     * @param int $userId
     * @param string $code
     * @param string|null $from
     * @param string|null $to
     */
    public function handle(int $userId, string $code, string $from = null, string $to = null): void;
}
