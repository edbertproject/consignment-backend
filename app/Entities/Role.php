<?php

namespace App\Entities;

use Spatie\Permission\Models\Role as SpatieRole;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Role.
 *
 * @package namespace App\Entities;
 */
class Role extends SpatieRole implements Transformable
{
    use TransformableTrait;

    protected $guard_name = 'api';

}
