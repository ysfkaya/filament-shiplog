<?php

namespace Ysfkaya\ShipLog\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\ShipLog\Database\Factories\ReleaseFactory;
use Ysfkaya\ShipLog\Enums\ReleaseStatus;

/**
 * @property string $version
 * @property ?string $title
 * @property ?string $body
 * @property ?CarbonImmutable $released_at
 * @property ReleaseStatus $status
 * @property ?array<int, string> $environments
 * @property bool $yanked
 */
class Release extends Model
{
    /** @use HasFactory<ReleaseFactory> */
    use HasFactory;

    protected $guarded = [];

    public function getTable(): string
    {
        return $this->table ?? config('shiplog.table', 'shiplog_releases');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'released_at' => 'immutable_date',
            'status' => ReleaseStatus::class,
            'environments' => 'array',
            'yanked' => 'boolean',
        ];
    }

    /**
     * Published, and not scheduled for a future date.
     *
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query
            ->where('status', ReleaseStatus::Published)
            ->where(
                fn (Builder $query) => $query
                    ->whereNull('released_at')
                    ->orWhereDate('released_at', '<=', now())
            );
    }

    protected static function newFactory(): ReleaseFactory
    {
        return ReleaseFactory::new();
    }
}
