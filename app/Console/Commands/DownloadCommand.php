<?php

namespace App\Console\Commands;

use App\Exceptions\VideoDownloadFailedException;
use App\Exceptions\VideoProcessingFailedException;
use App\Services\Contracts\VideoService as VideoServiceContract;
use Illuminate\Console\Command;

/**
 * Class DownloadCommand
 * @package App\Console\Commands
 */
class DownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt:download {user} {--code=} {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download video';

    /**
     * @var VideoServiceContract
     */
    protected VideoServiceContract $service;

    /**
     * DownloadCommand constructor.
     * @param VideoServiceContract $service
     */
    public function __construct(VideoServiceContract $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = intval($this->argument('user'));

        try {
            $this->service->handle($userId, $this->option('code'), $this->option('from'), $this->option('to'));
        } catch (VideoDownloadFailedException | VideoProcessingFailedException $e) {
            $this->error($e->getMessage());
        }

        return 0;
    }
}
