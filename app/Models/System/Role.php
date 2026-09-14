<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

#[Table(name: 'roles', key: 'id', keyType: 'string')]
#[WithoutIncrementing]
class Role extends SpatieRole
{
    use HasFactory;

    // Auto-generates UUIDv7 on creation
    use HasUuids;
}
