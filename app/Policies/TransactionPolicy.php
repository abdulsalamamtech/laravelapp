<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_transaction');
    }

    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('view_transaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_transaction');
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('update_transaction');
    }

    public function delete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('delete_transaction');
    }

    public function restore(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('restore_transaction');
    }

    public function forceDelete(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('force_delete_transaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_transaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_transaction');
    }

    public function replicate(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->can('replicate_transaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_transaction');
    }
}
