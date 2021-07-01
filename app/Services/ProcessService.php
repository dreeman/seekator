<?php

namespace App\Services;

use App\Exceptions\VideoDownloadFailedException;
use App\Exceptions\VideoProcessingFailedException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Services\Contracts\ProcessService as ProcessServiceContract;

/**
 * Class ProcessService
 * @package App\Services
 */
class ProcessService implements ProcessServiceContract
{
    /**
     * @param string $code
     * @return array
     * @throws VideoDownloadFailedException
     */
    public function youtubeDownload(string $code): array
    {
        $outputTemplate = storage_path('app/tmp/') . md5(random_int(100000, 999999) . microtime()) . '.%(ext)s';
        $commandLine = "LC_ALL=en_US.UTF-8 youtube-dl --continue --format=best --print-json --output='{$outputTemplate}' {$code}";

        try {
            $result = $this->run($commandLine);
        } catch (ProcessFailedException $e) {
            throw new VideoDownloadFailedException('Video download failed.', 0, $e);
        }

        $result = json_decode($result);

        return [
            'title' => $result->title,
            'path' => $result->_filename,
            'code' => $result->id,
        ];
    }

    /**
     * @param string $filePath
     * @param string|null $from
     * @param string|null $to
     * @return string
     * @throws VideoProcessingFailedException
     */
    public function ffmpegTrim(string $filePath, string $from = null, string $to = null): string
    {
        $pathInfo = pathinfo($filePath);
        $newFilePath = sprintf('%s/%s.%s',
            $pathInfo['dirname'],
            md5(random_int(100000, 999999) . microtime()),
            $pathInfo['extension']
        );

        $fromParam = $from ? " -ss {$from}" : '';
        $toParam = $to ? " -to {$to}" : '';
        $commandLine = "ffmpeg{$fromParam}{$toParam} -async 1 -y -i {$filePath} {$newFilePath}";

        try {
            $this->run($commandLine);
        } catch (ProcessFailedException $e) {
            File::delete($newFilePath);
            throw new VideoProcessingFailedException('Video processing failed.', 0, $e);
        }

        return $newFilePath;
    }

    /**
     * @param string $commandLine
     * @return string
     */
    protected function run(string $commandLine): string
    {
        $process = Process::fromShellCommandline($commandLine)->setTimeout(null);
        $process->run();
        if (!$process->isSuccessful()) {
            Log::error('FFMPEG Exit code: '. $process->getExitCode());
            Log::error($process->getErrorOutput());
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}
