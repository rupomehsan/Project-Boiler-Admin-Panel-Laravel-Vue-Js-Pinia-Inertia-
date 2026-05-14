<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Supports single slug: permission:role-view
     * Supports OR logic:    permission:role-details|role-edit
     */
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!auth('api')->check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        /** @var \Modules\Management\UserManagement\User\Database\Models\Model $user */
        $user = auth('api')->user();
        $role = $user->role()->with('permissions')->first();

        if ($role && $role->serial_no == 1) {
            return $next($request);
        }

        $slugs = explode('|', $permission);

        if (!$role || !$role->hasAnyPermission($slugs)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        return $next($request);
    }
}
