<?php

namespace Tests\Unit;

use App\Entities\Invoice;
use App\Services\NumberSettingService;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {
        print_r(NumberSettingService::generate(Invoice::class));
    }
}
