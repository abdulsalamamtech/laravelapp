<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChatMessage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ChatMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_chat::message');
    }

    public function view(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('view_chat::message');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_chat::message');
    }

    public function update(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('update_chat::message');
    }

    public function delete(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('delete_chat::message');
    }

    public function restore(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('restore_chat::message');
    }

    public function forceDelete(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('force_delete_chat::message');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_chat::message');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_chat::message');
    }

    public function replicate(AuthUser $authUser, ChatMessage $chatMessage): bool
    {
        return $authUser->can('replicate_chat::message');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_chat::message');
    }
}
