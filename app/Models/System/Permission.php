<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Table(name: 'permissions', key: 'id', keyType: 'string')]
#[WithoutIncrementing]
class Permission extends SpatiePermission
{
    use HasFactory;

    // Auto-generates UUIDv7 on creation
    use HasUuids;
}
