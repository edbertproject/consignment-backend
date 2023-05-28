<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrdersController;
use App\Services\OrderService;
use Illuminate\Console\Command;

class CheckOrderStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:order-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check order status and update when pass interval time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        OrderService::checkAndUpdateExpiredStatus();
        return Command::SUCCESS;
    }
}
