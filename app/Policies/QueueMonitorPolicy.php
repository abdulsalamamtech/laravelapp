<?php

declare(strict_types=1);

namespace App\Policies;

use Croustibat\FilamentJobsMonitor\Models\QueueMonitor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class QueueMonitorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_queue::monitor');
    }

    public function view(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('view_queue::monitor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_queue::monitor');
    }

    public function update(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('update_queue::monitor');
    }

    public function delete(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('delete_queue::monitor');
    }

    public function restore(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('restore_queue::monitor');
    }

    public function forceDelete(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('force_delete_queue::monitor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_queue::monitor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_queue::monitor');
    }

    public function replicate(AuthUser $authUser, QueueMonitor $queueMonitor): bool
    {
        return $authUser->can('replicate_queue::monitor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_queue::monitor');
    }
}
