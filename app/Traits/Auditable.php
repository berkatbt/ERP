<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::saveAuditLog($model, 'created');
        });

        static::updated(function ($model) {
            self::saveAuditLog($model, 'updated');
        });

        static::deleted(function ($model) {
            self::saveAuditLog($model, 'deleted');
        });
    }

    protected static function saveAuditLog($model, string $action): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $user = Auth::user();
        $url = null;
        $ipAddress = null;
        $userAgent = null;

        if (! app()->runningInConsole()) {
            $request = request();
            $url = $request->fullUrl();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
        }

        AuditLog::create([
            'user_id' => $user?->id,
            'branch_id' => $user?->branch_id,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'description' => sprintf('%s %s pada %s', ucfirst($action), class_basename($model), now()->format('Y-m-d H:i:s')),
            'old_values' => $action === 'updated' ? $model->getOriginal() : null,
            'new_values' => $model->getAttributes(),
            'url' => $url,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
