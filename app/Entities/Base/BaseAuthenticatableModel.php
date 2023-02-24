<?php

namespace App\Entities\Base;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Wildside\Userstamps\Userstamps;

class BaseAuthenticatableModel extends Authenticatable implements Transformable, HasMedia, Auditable {
    use TransformableTrait, HasApiTokens, HasFactory,
        Notifiable, HasRoles, InteractsWithMedia,
        \OwenIt\Auditing\Auditable, SoftDeletes, Userstamps;

    protected $guard_name = 'api';
}
