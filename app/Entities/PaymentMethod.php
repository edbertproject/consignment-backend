<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class PaymentMethod.
 *
 * @package namespace App\Entities;
 */
class PaymentMethod extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'code',
        'name',
        'description',
        'xendit_code',
        'is_enabled',
    ];

    /**
     * @return MorphOne
     */
    public function logo()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'logo');
    }

    public function paymentMethodInstructions()
    {
        return $this->hasMany(PaymentMethodInstruction::class);
    }

}
