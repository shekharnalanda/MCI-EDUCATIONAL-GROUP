<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminAccessScope
{
    public function applyInstitutionScope(Builder $query, User $user, string $column = 'institution_id'): Builder
    {
        if ($user->isMasterAdmin() || $user->role === 'central_manager') {
            return $query;
        }

        return $query->where($column, $user->institution_id ?: 0);
    }

    public function canAccessInstitution(User $user, ?int $institutionId): bool
    {
        return $user->isMasterAdmin()
            || $user->role === 'central_manager'
            || ($user->institution_id && (int) $user->institution_id === (int) $institutionId);
    }

    public function canManageSettings(User $user): bool
    {
        return $user->isMasterAdmin();
    }

    public function canDelete(User $user): bool
    {
        return in_array($user->role, ['master_admin','central_manager'], true);
    }
}
