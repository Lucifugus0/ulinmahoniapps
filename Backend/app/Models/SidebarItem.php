<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'route',
        'permission_id',
        'parent_id',
        'order',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function children()
    {
        return $this->hasMany(SidebarItem::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(SidebarItem::class, 'parent_id');
    }
}
