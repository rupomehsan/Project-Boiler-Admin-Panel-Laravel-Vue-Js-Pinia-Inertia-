<?php

namespace Modules\Management\UserManagement\Role\Controller;

use Modules\Management\UserManagement\Role\Database\Models\Permission;
use Modules\Management\UserManagement\Role\Database\Models\Model as Role;
use App\Http\Controllers\Controller as ControllersController;

class PermissionController extends ControllersController
{
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::active()
                ->orderBy('category')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'route', 'description', 'category']);
            
            return response()->json([
                'status' => 'success',
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRolePermissions($slug)
    {
        try {
            $role = Role::where('slug', $slug)->firstOrFail();
            
            $permissionIds = $role->permissions()->pluck('permission_id')->toArray();
            
            return response()->json([
                'status' => 'success',
                'data' => $permissionIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }
    }

    public function assignPermissionsToRole($slug)
    {
        try {
            $role = Role::where('slug', $slug)->firstOrFail();
            $permissionIds = request()->input('permissions', []);
            
            // Sync permissions (add/remove as needed)
            $role->permissions()->sync($permissionIds);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Permissions assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
