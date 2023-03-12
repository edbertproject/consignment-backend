<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ApiExternalLog.
 *
 * @package namespace App\Entities;
 */
class ApiExternalLog extends BaseModel
{
    protected $fillable = [
        'vendor',
        'url',
        'request_header',
        'request_body',
        'response'
    ];
}
