<?php

namespace App\Http\Controllers\Public;

use App\Entities\Cart;
use App\Entities\UserAddress;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingCalculateRequest;
use App\Http\Resources\BaseResource;
use App\Services\ShippingService;
use App\Utils\Constants;

class ShippingsController extends Controller
{
    public function calculate(ShippingCalculateRequest $request) {
        $cart = Cart::find($request->get('cart_id'));

        $product = $cart->product;
        $originId = @$product->partner->city_id ?? Constants::RAJA_ONGKIR_DEFAULT_CITY_ID;

        $userAddress = UserAddress::find($request->get('user_address_id'));

        return BaseResource::collection(ShippingService::checkAllCourierCost($originId, $userAddress->city_id, $product->weight, Constants::RAJA_ONGKIR_COURIERS));
    }
}
