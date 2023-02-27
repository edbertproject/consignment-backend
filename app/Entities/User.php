<?php

namespace App\Entities;

use App\Entities\Base\BaseAuthenticatableModel;
use App\Notifications\ResetPasswordRequestNotification;
use App\Utils\Constants;
use App\Utils\Traits\WithAbility;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public $appends = [
        'status',
        'can_update',
        'can_delete',
        'can_approve'
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

    public function partner() {
        return $this->hasOne(Partner::class, 'user_id');
    }

    public function getTotalNotificationAttribute() {
        return count($this->notifications);
    }

    public function getTotalUnreadNotificationAttribute() {
        return $this->unreadNotifications()->count();
    }

    public function findForPassport($username) {
        return $this->where(function ($w) use($username){
            $w->where('email', $username)->orWhere('username', $username);
        })->where('is_active', 1)->first();
    }

    public function getCanUpdateAttribute() {
        return $this->roles()
            ->where('role_id','!=',Constants::ROLE_PUBLIC_ID)
            ->exists();
    }

    public function getCanDeleteAttribute() {
        return $this->getCanUpdateAttribute();
    }

    public function getCanApproveAttribute() {
        if ($this->roles()
            ->where('role_id',Constants::ROLE_PARTNER_ID)
            ->exists()) {
            return $this->partner->status === Constants::PARTNER_STATUS_WAITING_APPROVAL;
        }

        return false;
    }

    public function getStatusAttribute() {
        if ($this->roles()
            ->where('role_id',Constants::ROLE_PARTNER_ID)
            ->exists()) {
            return $this->partner->status;
        }

        if (!$this->is_active) {
            return 'Inactive';
        }

        return 'Active';
    }
}
