<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Enregistrer une action dans les logs d'audit.
     */
    public static function log(string $action, ?string $resourceType = null, $resourceId = null, ?string $description = null, array $metadata = [])
    {
        return AuditLog::create([
            'user_id' => Auth::id() ?? 1, // Fallback vers system si non authentifié (ex: login failed)
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
