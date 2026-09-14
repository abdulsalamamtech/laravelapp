<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_activity::log');
    }

    public function view(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('view_activity::log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_activity::log');
    }

    public function update(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('update_activity::log');
    }

    public function delete(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('delete_activity::log');
    }

    public function restore(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('restore_activity::log');
    }

    public function forceDelete(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('force_delete_activity::log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_activity::log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_activity::log');
    }

    public function replicate(AuthUser $authUser, Activity $activity): bool
    {
        return $authUser->can('replicate_activity::log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_activity::log');
    }
}
