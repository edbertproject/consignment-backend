<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\ProductCriteria;
use App\Entities\Product;
use App\Events\ProductNewBid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ProductBidRequest;
use App\Http\Resources\Public\ProductResource;
use App\Jobs\ProductBidStoreJob;
use App\Services\ExceptionService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ProductsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(ProductRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            ProductCriteria::class
        ];

        $this->indexResource = ProductResource::class;
        $this->showResource = ProductResource::class;
    }

    public function bid(ProductBidRequest $request, int $id) {
        try {
            if ($this->repository->where('id',$id)
                ->where('type','!=',Constants::PRODUCT_TYPE_CONSIGN)
                ->where('status',Constants::PRODUCT_STATUS_ACTIVE)
                ->count() < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product invalid to bid.'
                ]);
            }

            $redisIdentifier= 'product_bid:'.$id;
            $amount = $request->get('amount');

            $bids = json_decode(Redis::get($redisIdentifier));
            if (!empty($bids)) {
                $lastBid = collect($bids)->last();
                if ($amount <= $lastBid->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bid invalid.'
                    ]);
                }
            }

            $newBid = [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'product_id' => $id,
                'amount' => $amount,
                'date_time' => Carbon::now()
            ];

            $bids[] = $newBid;

            Redis::set($redisIdentifier, json_encode($bids));

            ProductNewBid::dispatch($newBid);
            // job queue for save to db
            ProductBidStoreJob::dispatch($newBid, $id);

            return response()->json([
                'success' => true,
                'message' => 'Bid stored.'
            ]);
        } catch (\Exception $e) {
            return ExceptionService::responseJson($e);
        }
    }
}
