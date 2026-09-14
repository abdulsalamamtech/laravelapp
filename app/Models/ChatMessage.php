<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'name',
    'email',
    'message',
    'ip_address',
])]
#[WithoutIncrementing]
class ChatMessage extends Model
{
    use HasFactory;
    // Auto-generates UUIDv7 on creation
    use HasUuids;

    use SoftDeletes;

    // uuid as primary key
    protected $primaryKey = 'id';

    protected $keyType = 'string';
}
