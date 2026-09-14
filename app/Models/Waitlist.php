<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Activitylog\LogOptions;

#[Fillable([
    'type',
    'source',
    'ip_address',
    'name',
    'email',
    'status',
    'invited_at',
    'verified_at',
])]
#[Hidden([
    'data',
])]
#[WithoutIncrementing]
class Waitlist extends Model
{
    use AuthenticationLoggable;
    use HasFactory;

    // Auto-generates UUIDv7 on creation
    use HasUuids;
    use SoftDeletes;

    // uuid as primary key
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('plan information')
            ->setDescriptionForEvent(function (string $eventName): string {
                if ($eventName === 'created') {
                    return 'plan created';
                }

                if ($eventName === 'updated') {
                    return 'plan updated';
                }

                if ($eventName === 'deleted') {
                    return 'plan deleted';
                }

                return 'plan updated';
            });
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'verified_at' => 'datetime',
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
