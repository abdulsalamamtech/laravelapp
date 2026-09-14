<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

#[WithoutIncrementing]
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // Auto-generates UUIDv7 on creation (handled by the HasUuids trait)
    use HasUuids;

    // uuid as primary key
    protected $primaryKey = 'id';

    protected $keyType = 'string';
}
