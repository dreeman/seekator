<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Creative extends Model
{
    public const STATUS_DONE = 0;
    public const STATUS_QUEUED = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_FAILED = 3;

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'vendor',
        'vendor_code',
        'title',
        'filename',
        'link',
        'ext',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return string
     */
    public function getFileLink(): string
    {
        return "/media/{$this->user->id}/{$this->filename}";
    }

    /**
     * @return bool
     */
    public function scopeIsInProgress(): bool
    {
        return $this->status == self::STATUS_IN_PROGRESS;
    }
}
