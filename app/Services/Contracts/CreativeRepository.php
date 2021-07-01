<?php

namespace App\Services\Contracts;

use App\Models\Creative;
use Illuminate\Support\Collection;

interface CreativeRepository
{
    /**
     * @param int|null $userId
     * @return self
     */
    public function setUser(?int $userId = null): self;

    /**
     * @return int
     */
    public function getReadyStatus(): int;

    /**
     * @return Collection
     */
    public function getCreatives(): Collection;

    /**
     * @param array $data
     * @return Creative
     */
    public function create(array $data): Creative;

    /**
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data): void;

    /**
     * @param int $id
     * @param string $code
     * @param string $title
     * @param string $path
     */
    public function saveFile(int $id, string $code, string $title, string $path): void;

    /**
     * @param int $status
     * @return int
     */
    public function getCountByStatus(int $status): int;

    /**
     * @return Creative
     */
    public function getNextFromQueue(): Creative;
}
