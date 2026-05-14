<?php

namespace Modules\Management\UserManagement\Role\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    
    protected $table = "permissions";
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(
            Model::class,
            'role_permission',
            'permission_id',
            'role_id'
        );
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeInactive($q)
    {
        return $q->where('status', 'inactive');
    }
}
