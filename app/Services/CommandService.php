<?php

namespace App\Services;

use App\Services\Contracts\CommandService as CommandServiceContract;
use App\Services\Contracts\CreativeRepository as DataRepositoryContract;
use Illuminate\Support\Facades\File;

/**
 * Class CommandService
 * @package App\Services
 */
class CommandService implements CommandServiceContract
{
    /**
     * @var DataRepositoryContract
     */
    protected DataRepositoryContract $repository;

    /**
     * CommandService constructor.
     * @param DataRepositoryContract $repository
     */
    public function __construct(DataRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $userId
     * @param string|null $code
     * @param string|null $from
     * @param string|null $to
     */
    public function startProcessingCommand(int $userId, string $code = null, string $from = null, string $to = null): void
    {
        $this->repository->setUser($userId);

        $code = $code ? ' --code=' . $code : '';
        $from = $from ? ' --from=' . $from : '';
        $to = $to ? ' --to=' . $to : '';

        $commandLine = "php " . base_path('artisan') . " mt:download{$code}{$from}{$to} {$userId} > /dev/null 2>&1 &";

        File::append(storage_path('logs/commands.log'), $commandLine);

        exec($commandLine);
    }
}
