<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use App\Utils\Constants;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Product.
 *
 * @package namespace App\Entities;
 */
class Product extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_category_id',
        'type',
        'participant',
        'winner_id',
        'name',
        'price',
        'start_price',
        'multiplied_price',
        'available_quantity',
        'desired_price',
        'start_date',
        'end_date',
        'weight',
        'quantity',
        'long_dimension',
        'wide_dimension',
        'high_dimension',
        'condition',
        'warranty',
        'description',
        'cancel_reason',
        'status',
        'partner_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    public $appends = [
        'seller_name',
        'seller_city',
        'current_bid',
        'can_delete',
        'can_update',
        'can_approve',
        'can_reject',
        'can_approve_cancel',
        'can_cancel',
    ];

    /**
     * @return MorphOne
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'photos')
            ->orderBy('id');
    }

    /**
     * @return MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'photos');
    }

    public function bids() {
        return $this->hasMany(ProductBid::class);
    }

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class);
    }

    public function winner() {
        return $this->belongsTo(User::class,'winner_id','id');
    }

    public function partner() {
        return $this->belongsTo(Partner::class);
    }

    public function getCurrentBidAttribute() {
        return @$this->bids()->latest()->first()->amount;
    }

    public function getCanUpdateAttribute() {
        if (!empty($this->partner_id)) {
            return $this->partner_id === Auth::id();
        }

        return true;
    }

    public function getCanDeleteAttribute() {
        return empty($this->partner_id) && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    public function getCanApproveAttribute() {
        return $this->status === Constants::PRODUCT_STATUS_WAITING_APPROVAL && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    public function getCanRejectAttribute() {
        return $this->can_approve;
    }

    public function getCanApproveCancelAttribute() {
        return $this->status === Constants::PRODUCT_STATUS_WAITING_CANCEL_APPROVAL && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    public function getCanCancelAttribute() {
        return $this->status === Constants::PRODUCT_STATUS_APPROVED || $this->status === Constants::PRODUCT_STATUS_WAITING_APPROVAL;
    }

    public function getSellerNameAttribute() {
        return @$this->partner->user->name ?? config('app.name');
    }

    public function getSellerCityAttribute() {
        return @$this->partner->city->name ?? @City::where('raja_ongkir_id',Constants::RAJA_ONGKIR_DEFAULT_CITY_ID)->first()->name;
    }
}
