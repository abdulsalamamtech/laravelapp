<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Activitylog\LogOptions;

#[Fillable([
    'first_name',
    'last_name',
    'other_name',
    'email',
    'phone_number',
    'subject',
    'purpose',
    'organization',
    'message',
    'feedback',
    'status',
    'updated_by',

    // Metadata
    'raw_data',
])]
#[WithoutIncrementing]
class Contact extends Model
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
            ->useLogName('contact message information')
            ->setDescriptionForEvent(function (string $eventName): string {
                if ($eventName === 'created') {
                    return 'contact message created';
                }

                if ($eventName === 'updated') {
                    return 'contact message updated';
                }

                if ($eventName === 'deleted') {
                    return 'contact message deleted';
                }

                return 'contact message updated';
            });
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
