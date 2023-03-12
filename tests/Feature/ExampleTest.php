<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Entities\Invoice;
use App\Entities\Order;
use App\Http\Controllers\Public\OrdersController;
use App\Services\NumberSettingService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xendit\VirtualAccounts;
use Xendit\Xendit;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        print_r(urlencode('INV/202303/00001'));
    }

    public function test_xendit() {
        OrdersController::payXendit(Invoice::find('5064e897-06ac-4a05-87f5-84a09b10f29d'));
    }
}
