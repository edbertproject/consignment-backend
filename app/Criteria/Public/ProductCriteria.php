<?php

namespace App\Criteria\Public;

use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ProductCriteria.
 *
 * @package namespace App\Criteria\Public;
 */
class ProductCriteria implements CriteriaInterface
{
    public function __construct(protected Request $request) {

    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
//        if (Auth::check()) {
//            $model = $model->where(function ($where) {
//                $where->where('partner_id','!=',@Auth::user()->partner->id)->orWhereNull('partner_id');
//            });
//        }

        if ($variety = $this->request->get('variety') ?? null) {
            if ($variety === 'featuring') {
                $model = $model->where('start_date','<=',Carbon::now())
                    ->where('end_date','>=',Carbon::now())
                    ->where('status',Constants::PRODUCT_STATUS_ACTIVE)
                    ->orderByDesc('id');
            } else if ($variety === 'hot') {
                $model = $model->where('status',Constants::PRODUCT_STATUS_ACTIVE)
                    ->where('start_date','<=',Carbon::now())
                    ->where('end_date','>=',Carbon::now())
                    ->orderBy('start_date');
            } else if ($variety === 'incoming') {
                $model = $model->where('start_date','>',Carbon::now())
                    ->where('status', Constants::PRODUCT_STATUS_APPROVED)
                    ->orderBy('end_date');
            }
        }

        if ($minPrice = $this->request->get('min_price') ?? null) {
            $model = $model->where(function ($where) use($minPrice) {
                $where->where(function ($q) use($minPrice) {
                    $q->where('type',Constants::PRODUCT_TYPE_CONSIGN)
                        ->where('price','>=',$minPrice);
                })->orWhere(function ($q) use($minPrice) {
                    $q->where('type','!=',Constants::PRODUCT_TYPE_CONSIGN)
                        ->where('start_price','>=',$minPrice);
                });
            });
        }

        if ($maxPrice = $this->request->get('max_price') ?? null) {
            $model = $model->where(function ($where) use($maxPrice) {
                $where->where(function ($q) use($maxPrice) {
                    $q->where('type',Constants::PRODUCT_TYPE_CONSIGN)
                        ->where('price','<=',$maxPrice);
                })->orWhere(function ($q) use($maxPrice) {
                    $q->where('type','!=',Constants::PRODUCT_TYPE_CONSIGN)
                        ->where('start_price','<=',$maxPrice);
                });
            });
        }

        if ($categories = $this->request->get('categories') ?? null) {
            $model = $model->whereIn('product_category_id', $categories);
        }

        if ($type = $this->request->get('type') ?? null) {
            $model = $model->where('type', $type);
        }

        return $model->where(function ($where) {
            $where->where(function ($inner) {
                $inner->where('type','=',Constants::PRODUCT_TYPE_SPECIAL_AUCTION)
                    ->whereHas('participants', function ($p) {
                        $p->where('user_id', Auth::id());
                    });
            })->orWhere('type','!=',Constants::PRODUCT_TYPE_SPECIAL_AUCTION);
        })->whereIn('status',[
            Constants::PRODUCT_STATUS_APPROVED,
            Constants::PRODUCT_STATUS_ACTIVE,
            Constants::PRODUCT_STATUS_SOLD,
            // Constants::PRODUCT_STATUS_CLOSED
        ]);
    }
}
