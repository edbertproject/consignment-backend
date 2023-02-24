<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserToken.
 *
 * @package namespace App\Entities;
 */
class UserToken extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'token'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
