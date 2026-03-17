<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone_number',
        'nik',
        'username',
        'email',
        'password',
        'is_admin',
        'property_id',
        'user_type',
        'role_id',
        'status',
        'created_by',
        'updated_by',
        'profile_picture',
        'profile_photo_path',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_type' => 'integer',
        'is_admin' => 'integer',
        'property_id' => 'integer',
        'role_id' => 'integer',
        'status' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class);
    // }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'user_id', 'permission_id');
    }

    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function isSuperAdmin()
    {
        return $this->is_admin === 1;
    }

    /**
     * Check if user is HO type based on user_type column
     * user_type: 0=HO, 1=Site
     */
    public function isHORole()
    {
        return $this->user_type === 0;
    }

    /**
     * Check if user is Site type based on user_type column
     * user_type: 0=HO, 1=Site
     */
    public function isSiteRole()
    {
        return $this->user_type === 1;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roleName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->name === $roleName;
    }

    /**
     * Check if user is a Finance-only role
     * Finance-only roles can only view financial information
     */
    public function isFinanceOnlyRole()
    {
        if (!$this->role) {
            return false;
        }

        $financeOnlyRoles = ['Finance', 'Finance HO', 'Finance site'];
        return in_array($this->role->name, $financeOnlyRoles);
    }

    /**
     * Check if user can view all properties
     * Super Admin, HO user_type, and HO roles can view all properties
     */
    public function canViewAllProperties()
    {
        return $this->isSuperAdmin() || $this->isHO() || $this->isHORole();
    }

    /**
     * Get the property ID(s) this user can access
     * Returns null for users who can access all properties
     */
    public function getAccessiblePropertyId()
    {
        // Site users (user_type = 1) should only access their assigned property
        // This takes priority over is_admin flag
        if ($this->isSite() && $this->property_id) {
            return $this->property_id;
        }

        // Super Admin and HO roles can access all properties
        if ($this->canViewAllProperties()) {
            return null;
        }

        // Fallback to property_id if set
        return $this->property_id;
    }

    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    // Chat Relationships
    public function chatParticipations()
    {
        return $this->hasMany(ChatParticipant::class, 'user_id');
    }

    public function chatConversations()
    {
        return $this->belongsToMany(ChatConversation::class, 't_chat_participants', 'user_id', 'conversation_id')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Check if user is HO (Head Office) type
     * @return bool
     */
    public function isHO()
    {
        return $this->user_type === 0;
    }

    /**
     * Check if user is Site type
     * @return bool
     */
    public function isSite()
    {
        return $this->user_type === 1;
    }

    /**
     * Get user type label
     * @return string
     */
    public function getUserTypeLabel()
    {
        return $this->user_type === 0 ? 'HO' : 'Site';
    }

    /**
     * Get user type icon
     * @return string
     */
    public function getUserTypeIcon()
    {
        return $this->user_type === 0 ? '🏢' : '📍';
    }
}
