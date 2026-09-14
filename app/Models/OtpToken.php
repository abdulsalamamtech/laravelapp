<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'phone_number',
    'email',
    'type',
    'token',
    'expires',
    'failed_attempts',
])]
class OtpToken extends Model
{
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'expires' => 'datetime',
        ];
    }
}
