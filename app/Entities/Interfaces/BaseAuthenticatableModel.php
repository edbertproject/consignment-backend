<?php

namespace App\Entities\Interfaces;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

class BaseAuthenticatableModel extends Authenticatable implements Transformable, Auditable, HasMedia {
    use TransformableTrait, HasApiTokens, HasFactory,
        Notifiable, HasRoles, InteractsWithMedia,
        Userstamps, \OwenIt\Auditing\Auditable;

    protected $guard_name = 'api';
}
