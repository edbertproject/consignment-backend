<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\ProductCriteria;
use App\Entities\Product;
use App\Entities\ProductBid;
use App\Events\ProductNewBid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ProductBidRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Public\ProductResource;
use App\Jobs\ProductBidStoreJob;
use App\Repositories\ProductBidRepository;
use App\Services\ExceptionService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
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

    public function __construct(ProductRepository $repository, protected ProductBidRepository $bidRepository) {
        $this->__rest($repository);


        $this->indexCriterias = [
            ProductCriteria::class
        ];

        $this->indexResource = ProductResource::class;
        $this->showResource = ProductResource::class;
    }

    public function show(Request $request, $id)
    {
        foreach ($this->indexCriterias as $indexCriteria) {
            $this->repository->pushCriteria(new $indexCriteria($request));
        }

        $user = $this->repository->findByField('slug',$id)->first();

        return new $this->showResource($user);
    }

    public function bid(ProductBidRequest $request, int $id) {
        try {
            if ($this->repository->where('products.id',$id)
                ->where(function ($where) {
                    $where->where('type',Constants::PRODUCT_TYPE_AUCTION)
                        ->orWhere(function ($or) {
                            $or->where('type', Constants::PRODUCT_TYPE_SPECIAL_AUCTION)
                                ->whereHas('relationParticipants', function ($p) {
                                    $p->where('product_participants.user_id', Auth::id());
                                });
                        });
                })->where('status',Constants::PRODUCT_STATUS_ACTIVE)
                ->count() < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product invalid to bid.'
                ], 422);
            }

            $redisIdentifier = 'product_bid:'.$id;
            $amount = $request->get('amount');

            $bids = json_decode(Redis::get($redisIdentifier));
            if (!empty($bids)) {
                $lastBid = collect($bids)->last();
                if ($amount <= $lastBid->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bid invalid.'
                    ], 422);
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

            Redis::set($redisIdentifier, json_encode([$newBid]));

            ProductBid::create($newBid);
            // pusher
            ProductNewBid::dispatch($newBid);

            return response()->json([
                'success' => true,
                'message' => 'Bid stored.'
            ]);
        } catch (\Exception $e) {
            return ExceptionService::responseJson($e);
        }
    }

    public function listBid(Request $request, int $id) {
        return BaseResource::collection(
            ProductBid::query()
            ->where('product_id',$id)
            ->orderBy('date_time', 'desc')
            ->paginate($request->get('per_page'),['*'], 'page', $request->get('page'))
        );
    }
}
