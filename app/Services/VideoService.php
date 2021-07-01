<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Exceptions\VideoDownloadFailedException;
use App\Exceptions\VideoProcessingFailedException;
use App\Models\Creative;
use App\Services\Contracts\VideoService as VideoServiceContract;
use App\Services\Contracts\CreativeRepository as DataRepositoryContract;
use App\Services\Contracts\ProcessService as ProcessServiceContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Class VideoService
 * @package App\Services
 */
class VideoService implements VideoServiceContract
{
    /**
     * @var DataRepositoryContract
     */
    protected DataRepositoryContract $repository;

    /**
     * @var ProcessServiceContract
     */
    protected ProcessServiceContract $process;

    /**
     * @var Creative|null
     */
    protected ?Creative $creative = null;

    /**
     * @var array|null
     */
    protected ?array $filesToDelete = null;

    /**
     * VideoService constructor.
     * @param DataRepositoryContract $repository
     * @param ProcessServiceContract $process
     */
    public function __construct(DataRepositoryContract $repository, ProcessServiceContract $process)
    {
        $this->repository = $repository;
        $this->process = $process;
    }

    /**
     * @param int $userId
     * @param string|null $code
     * @param string|null $from
     * @param string|null $to
     */
    public function handle(int $userId, string $code = null, string $from = null, string $to = null): void
    {
        $this->repository->setUser($userId);

        if (!is_null($code)) {
            $status = Creative::STATUS_QUEUED;

            $this->creative = $this->repository->create([
                'status' => $status,
                'vendor_code' => $code,
                'meta' => compact('from', 'to'),
            ]);

            broadcast(new MessageSent($userId, [
                'message' => 'process_done',
                'files' => $this->repository->getCreatives(),
            ]));
        }

        $this->startProcessing();
    }

    /**
     *
     */
    protected function startProcessing(): void
    {
        if ($this->repository->getCountByStatus(Creative::STATUS_IN_PROGRESS) === 0) {
            while ($this->repository->getCountByStatus(Creative::STATUS_QUEUED) > 0) {
                $this->processNextVideo();
            }
        }
    }

    /**
     *
     */
    protected function processNextVideo(): void
    {
        $this->creative = $this->repository->getNextFromQueue();
        $fileInfo = null;
        try {
            $this->creative->update([
                'status' => Creative::STATUS_IN_PROGRESS,
            ]);

            broadcast(new MessageSent($this->creative->user->id, [
                'message' => 'process_done',
                'files' => $this->repository->getCreatives(),
            ]));

            $fileInfo = $this->process->youtubeDownload($this->creative->vendor_code);
            $this->filesToDelete[] = $fileInfo['path'];

            $from = $this->creative->meta['from'] ?? null;
            $to = $this->creative->meta['to'] ?? null;

            if (!is_null($from) || !is_null($to)) {
                $fileInfo['path'] = $this->process->ffmpegTrim($fileInfo['path'], $from, $to);
                $this->filesToDelete[] = $fileInfo['path'];
            }

            $path = $fileInfo['path'];
            $this->repository->saveFile($this->creative->id, $this->creative->vendor_code, $fileInfo['title'], $path);
            File::delete($this->filesToDelete);
            $status = Creative::STATUS_DONE;

        } catch (VideoDownloadFailedException | VideoProcessingFailedException $e) {
            $status = Creative::STATUS_FAILED;
            Log::error($e->getMessage());

        } finally {
            $this->creative->update([
                'status' => $status,
            ]);

            broadcast(new MessageSent($this->creative->user->id, [
                'message' => 'process_done',
                'files' => $this->repository->getCreatives(),
            ]));
        }
    }
}
