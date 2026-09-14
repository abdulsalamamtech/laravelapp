<?php

namespace App\Models\Custom;

use Database\Factories\AllowedOriginFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @use HasFactory<AllowedOriginFactory>
 */
#[Fillable([
    'domain',
    'is_active',
])]
class AllowedOrigin extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('cors_allowed_origins');
        });

        static::deleted(function (): void {
            Cache::forget('cors_allowed_origins');
        });
    }
}
