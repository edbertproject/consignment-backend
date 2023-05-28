<?php

namespace App\Http\Controllers\Public;

use App\Entities\Cart;
use App\Entities\UserAddress;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingCalculateRequest;
use App\Http\Resources\BaseResource;
use App\Services\ShippingService;
use App\Utils\Constants;
use Illuminate\Support\Facades\Auth;

class ShippingsController extends Controller
{
    public function calculate(ShippingCalculateRequest $request) {
        $cart = Cart::query()->where('user_id', Auth::id())->first();

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 422);
        }

        $product = $cart->product;
        $originId = @$product->partner->city_id ?? Constants::RAJA_ONGKIR_DEFAULT_CITY_ID;

        $userAddress = UserAddress::find($request->get('user_address_id'));

        return BaseResource::collection(ShippingService::checkAllCourierCost($originId, $userAddress->city_id, $product->weight, Constants::RAJA_ONGKIR_COURIERS));
    }
}
