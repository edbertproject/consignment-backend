<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'slug',
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
        'can_wishlist',
        'seller_name',
        'seller_city',
        'current_bid',
        'current_bid_at',
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

    public function orders() {
        return $this->hasMany(Order::class);
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

    /**
     * @return BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'product_participants', 'product_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function relationParticipants()
    {
        return $this->hasMany(ProductParticipant::class,'product_id');
    }

    public function userWishlists() {
        return $this->belongsToMany(User::class, 'wishlists', 'product_id','user_id');
    }

    public function getCanWishlistAttribute() {
        return $this->userWishlists()
            ->when(Auth::check(), function ($q) {
                $q->where('user_id', Auth::id());
            })->doesntExist();
    }

    private function currentBid() {
       return @$this->bids()->latest()->first();
    }

    public function getCurrentBidAttribute() {
        return @$this->currentBid()->amount ?? $this->start_price;
    }

    public function getCurrentBidAtAttribute() {
        return @$this->currentBid()->date_time;
    }

    public function getCanUpdateAttribute() {
        $restrictedStatus = [
            Constants::PRODUCT_STATUS_SOLD,
            Constants::PRODUCT_STATUS_CLOSED,
            Constants::PRODUCT_STATUS_CANCEL_APPROVED,
            Constants::PRODUCT_STATUS_REJECTED,
            Constants::PRODUCT_STATUS_ACTIVE
        ];

        if (Auth::check() && Auth::user()->hasRole(Constants::ROLE_SUPER_ADMIN_ID)) {
            return !in_array($this->status, $restrictedStatus);
        }

        if (!empty($this->partner_id)) {
            return $this->partner_id === @Auth::user()->partner->id && $this->orders()->doesntExist() &&
                !in_array($this->status, $restrictedStatus);
        }

        return !in_array($this->status, $restrictedStatus);
    }

    public function getCanDeleteAttribute() {
        if (!Auth::check()) return false;

        return empty($this->partner_id) && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID) && $this->can_update;
    }

    public function getCanApproveAttribute() {
        if (!Auth::check()) return false;

        return $this->status === Constants::PRODUCT_STATUS_WAITING_APPROVAL && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    public function getCanRejectAttribute() {
        return $this->can_approve;
    }

    public function getCanApproveCancelAttribute() {
        if (!Auth::check()) return false;

        return $this->status === Constants::PRODUCT_STATUS_WAITING_CANCEL_APPROVAL && !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    public function getCanCancelAttribute() {
        return ($this->status === Constants::PRODUCT_STATUS_APPROVED || $this->status === Constants::PRODUCT_STATUS_WAITING_APPROVAL)
            && $this->orders()->doesntExist();
    }

    public function getSellerNameAttribute() {
        return @$this->partner->user->name ?? config('app.name');
    }

    public function getSellerCityAttribute() {
        return @$this->partner->city->name ?? @City::where('raja_ongkir_id',Constants::RAJA_ONGKIR_DEFAULT_CITY_ID)->first()->name;
    }
}
