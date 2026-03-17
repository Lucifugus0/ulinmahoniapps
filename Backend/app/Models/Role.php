<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Permission;

class Role extends Model
{
    use HasFactory;

    protected $table = 'm_roles';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function dashboardWidgets()
    {
        return $this->belongsToMany(DashboardWidget::class, 'role_dashboard_widgets', 'role_id', 'widget_id')
            ->withTimestamps()
            ->withPivot(['created_by', 'updated_by']);
    }

    public function hasWidgetAccess($widgetSlug)
    {
        return $this->dashboardWidgets()
            ->where('dashboard_widgets.slug', $widgetSlug)
            ->where('dashboard_widgets.is_active', 1)
            ->exists();
    }
}
