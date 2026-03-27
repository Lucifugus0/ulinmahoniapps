<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role model — reads from the shared m_roles table.
 * Used to identify user roles like 'Customer Service' for HQ CS routing.
 */
class Role extends Model
{
    protected $table = 'm_roles';

    protected $fillable = ['name'];

    /** Users with this role */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
