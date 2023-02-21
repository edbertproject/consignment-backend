<?php

namespace App\Entities;

use App\Entities\Interfaces\BaseAuthenticatableModel;
use App\Notifications\ResetPasswordRequestNotification;

class User extends BaseAuthenticatableModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'email_verified_at',
        'password',
        'old_password',
        'phone_number',
        'date_of_birth',
        'gender',
        'bank_name',
        'bank_number',
        'provider',
        'provider_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'old_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send email for reset password
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordRequestNotification($token));
    }

    public function userToken() {
        return $this->hasOne(UserToken::class, 'user_id');
    }

    public function getTotalNotificationAttribute() {
        return count($this->notifications);
    }

    public function getTotalUnreadNotificationAttribute() {
        return $this->unreadNotifications()->count();
    }

    public function findForPassport($username) {
        return $this->where('email', $username)
            ->where('is_active', 1)->first();
    }
}
