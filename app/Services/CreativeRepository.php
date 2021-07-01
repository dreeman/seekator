<?php

namespace App\Services;

use App\Models\Creative;
use App\Models\User;
use App\Services\Contracts\CreativeRepository as CreativeRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class DataRepository
 * @package App\Services
 */
class CreativeRepository implements CreativeRepositoryContract
{
    /**
     * @var string|null
     */
    protected ?string $dataFilePath = null;

    /**
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * @param int|null $userId
     * @return $this
     */
    public function setUser(int $userId = null): self
    {
        if (is_null($this->user)) {
            $this->user = is_null($userId) ? auth()->user() : User::findOrFail($userId);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getReadyStatus(): int
    {
        return $this->user->creatives()
                ->where('status',Creative::STATUS_IN_PROGRESS)
                ->count() == 0 ? 0 : 1;
    }

    /**
     * @return Collection
     */
    public function getCreatives(): Collection
    {
        return $this->user->creatives()->get();
    }

    /**
     * @param array $data
     * @return Creative
     */
    public function create(array $data): Creative
    {
        $data['user_id'] = $data['user_id'] ?? $this->user->id;
        $data['status'] = $data['status'] ?? Creative::STATUS_QUEUED;

        return Creative::create($data);
    }

    /**
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data): void
    {
        $this->user->creatives->find($id)->update($data);
    }

    /**
     * @param int $status
     * @return int
     */
    public function getCountByStatus(int $status): int
    {
        return $this->user->creatives()->where('status', $status)->count();
    }

    /**
     * @return Creative
     */
    public function getNextFromQueue(): Creative
    {
        return $this->user->creatives()->where('status', Creative::STATUS_QUEUED)->oldest()->first();
    }

    /**
     * @param int $id
     * @param string $code
     * @param string $title
     * @param string $path
     */
    public function saveFile(int $id, string $code, string $title, string $path): void
    {
        $ext = pathinfo($path)['extension'];
        $fileName = Str::slug($title) . '_' . $code . '.' . $ext;
        $fileLinkPublic = "/media/{$this->user->id}/{$fileName}";
        $filePathPublic = base_path('public' . $fileLinkPublic);

        if (!File::isDirectory(base_path("public/media/{$this->user->id}"))) {
            File::makeDirectory(base_path("public/media/{$this->user->id}"));
        }

        File::copy($path, $filePathPublic);

        $this->update($id, [
            'title' => $title,
            'filename' => $fileName,
            'link' => $fileLinkPublic,
            'path' => $path,
        ]);
    }
}
