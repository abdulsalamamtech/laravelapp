<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Waitlist;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WaitlistPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_waitlist');
    }

    public function view(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('view_waitlist');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_waitlist');
    }

    public function update(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('update_waitlist');
    }

    public function delete(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('delete_waitlist');
    }

    public function restore(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('restore_waitlist');
    }

    public function forceDelete(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('force_delete_waitlist');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_waitlist');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_waitlist');
    }

    public function replicate(AuthUser $authUser, Waitlist $waitlist): bool
    {
        return $authUser->can('replicate_waitlist');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_waitlist');
    }
}
