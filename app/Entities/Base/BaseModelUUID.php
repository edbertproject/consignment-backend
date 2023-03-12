<?php

namespace App\Entities\Base;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Ramsey\Uuid\Uuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class BaseModelUUID extends Model implements Transformable, Auditable, HasMedia {
    use TransformableTrait,
        InteractsWithMedia,
        \OwenIt\Auditing\Auditable,
        Userstamps;

    public $incrementing = false;

    public $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $data) {
            $data->id = Uuid::uuid4();
        });
    }
}
