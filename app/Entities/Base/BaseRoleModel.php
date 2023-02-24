<?php

namespace App\Entities\Base;

use OwenIt\Auditing\Contracts\Auditable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;
use Spatie\Permission\Models\Role as SpatieRole;

class BaseRoleModel extends SpatieRole implements Transformable, Auditable {
    use TransformableTrait, \OwenIt\Auditing\Auditable, Userstamps;

    protected $guard_name = 'api';
}
