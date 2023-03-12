<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;

class ProductService
{
    public static function updateAvailableQuantity($product, $availableQuantity) {

        $product->available_quantity = $availableQuantity;

        if ($availableQuantity === 0) {
            $product->status = Constants::PRODUCT_STATUS_SOLD;
        }

        $product->save();

        return true;
    }
}
