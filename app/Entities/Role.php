<?php

namespace App\Entities;

use App\Entities\Base\BaseRoleModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Role.
 */
class Role extends BaseRoleModel
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'is_admin',
        'guard_name'
    ];
}
