<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use App\Entities\Base\BaseModelUUID;
use App\Services\OrderService;
use App\Utils\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @package namespace App\Entities;
 */
class Order extends BaseModelUUID
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'number',
        'invoice_id',
        'user_id',
        'user_address_id',
        'product_id',
        'partner_id',
        'quantity',
        'price',
        'notes',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    public $appends = [
        'can_update',
        'can_delete',
        'status',
        'next_status',
        'can_process_next_status',
        'status_seller',
        'next_status_seller',
        'can_process_next_status_seller',
        'status_buyer',
        'next_status_buyer',
        'can_process_next_status_buyer',
    ];

    public function statuses() {
        return $this->hasMany(OrderStatus::class);
    }

    public function primaryStatuses() {
        return $this->statuses()
            ->where('type', Constants::ORDER_STATUS_TYPE_PRIMARY)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    public function sellerStatuses() {
        return $this->statuses()
            ->where('type', Constants::ORDER_STATUS_TYPE_SELLER)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    public function buyerStatuses() {
        return $this->statuses()
            ->where('type', Constants::ORDER_STATUS_TYPE_BUYER)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getCanUpdateAttribute() {
        return false;
    }

    public function getCanDeleteAttribute() {
        return false;
    }

    public function getStatusAttribute() {
        return @$this->primaryStatuses()->first()->status;
    }

    public function getStatusSellerAttribute() {
        return @$this->sellerStatuses()->first()->status;
    }

    public function getStatusBuyerAttribute() {
        return @$this->buyerStatuses()->first()->status;
    }

    public function getNextStatusAttribute() {
        return OrderService::getNextStatus($this)['status'] ?? null;
    }

    public function getCanProcessNextStatusAttribute() {
        return OrderService::getNextStatus($this)['can'] ?? null;
    }

    public function getNextStatusSellerAttribute() {
        return OrderService::getNextStatusSeller($this)['status'] ?? null;
    }

    public function getCanProcessNextStatusSellerAttribute() {
        return OrderService::getNextStatusSeller($this)['can'] ?? null;
    }

    public function getNextStatusBuyerAttribute() {
        return OrderService::getNextStatusBuyer($this)['status'] ?? null;
    }

    public function getCanProcessNextStatusBuyerAttribute() {
        return OrderService::getNextStatusBuyer($this)['can'] ?? null;
    }
}
