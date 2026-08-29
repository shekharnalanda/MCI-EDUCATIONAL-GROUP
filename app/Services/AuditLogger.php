<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function record(string $action, ?Model $model = null, array $oldValues = [], array $newValues = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'institution_id' => $model?->institution_id ?? auth()->user()?->institution_id,
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
        ]);
    }
}
