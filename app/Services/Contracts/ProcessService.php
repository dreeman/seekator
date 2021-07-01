<?php

namespace App\Services\Contracts;

interface ProcessService
{
    /**
     * @param string $code
     * @return array
     */
    public function youtubeDownload(string $code): array;

    /**
     * @param string $filePath
     * @param string|null $from
     * @param string|null $to
     * @return string
     */
    public function ffmpegTrim(string $filePath, string $from = null, string $to = null): string;
}
