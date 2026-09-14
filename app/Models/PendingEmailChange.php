<?php

namespace App\Models;

use App\Enums\EmailChangeCancelledReason;
use App\Enums\EmailChangeStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'old_email',
    'pending_email',
    'status',
    'initiated_at',
    'old_confirmed_at',
    'new_confirmed_at',
    'accepted_at',
    'effective_at',
    'changed_at',
    'cancelled_at',
    'cancelled_reason',
    'confirming_token_id',
    'ip_address',
    'user_agent',
])]
#[WithoutIncrementing]
class PendingEmailChange extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'status' => EmailChangeStatus::class,
            'cancelled_reason' => EmailChangeCancelledReason::class,
            'initiated_at' => 'datetime',
            'old_confirmed_at' => 'datetime',
            'new_confirmed_at' => 'datetime',
            'accepted_at' => 'datetime',
            'effective_at' => 'datetime',
            'changed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * The user requesting the email change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether the change is still actionable (not yet applied/cancelled/expired).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [EmailChangeStatus::PENDING, EmailChangeStatus::SCHEDULED], true);
    }
}
