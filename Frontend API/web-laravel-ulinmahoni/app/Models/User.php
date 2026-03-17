<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Get the password for the user.
     *
     * @return string
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function getAuthPassword()
    {
        // Only allow login if status is 1
        if ($this->status != 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact support for assistance.',
            ]);
        }
        
        // Check if email is verified
        // if (empty($this->email_verified_at)) {
        //     throw \Illuminate\Validation\ValidationException::withMessages([
        //         'email' => 'Please verify your email address before logging in. Check your email for the verification link.',
        //     ]);
        // }
        
        return $this->password;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'name',
        'email',
        'phone_number',
        'nik',
        'password',
        'status',
        'email_verified_at',
        'is_admin',
        'profile_photo_path',
        'is_google',
        'is_apple',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone_number',
        'status',
        'email_verified_at',
        'profile_photo_path',
        'profile_photo_url'
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'profile_picture'
    ];

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
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
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $notification = new VerifyEmailNotification;
        $notification->sendViaSMTP($this);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new \App\Notifications\PasswordResetNotification($token);
        $notification->sendViaSMTP($this);
    }

    // If you still need the full name somewhere in your application, you can keep this method
    // but it won't be included in JSON responses unless explicitly called
    /**
     * Get the user's first name.
     *
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return $value ?? '';
    }

    /**
     * Get the user's last name.
     *
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return $value ?? '';
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    
    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePictureAttribute()
    {
        if ($this->profile_photo_path) {
            // Use config('app.url') to get the base URL from .env
            $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
            return $baseUrl . '/storage/' . ltrim($this->profile_photo_path, '/');
        }
        
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = ($password);
    }

    /**
     * Chat relationships
     */
    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class, 'user_id', 'id');
    }

    public function chatConversations()
    {
        return $this->belongsToMany(ChatConversation::class, 't_chat_participants', 'user_id', 'conversation_id')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id', 'id');
    }
}
