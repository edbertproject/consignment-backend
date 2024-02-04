<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\User;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class SchedulersController extends Controller
{
    public function __construct()
    {
    }

    public function artisanScheduler() {
        Artisan::call('schedule:run');
    }
}
