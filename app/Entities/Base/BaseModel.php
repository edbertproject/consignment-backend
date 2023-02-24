<?php

namespace App\Entities\Base;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class BaseModel extends Model implements Transformable, Auditable, HasMedia {
    use TransformableTrait, InteractsWithMedia, \OwenIt\Auditing\Auditable, Userstamps;
}
