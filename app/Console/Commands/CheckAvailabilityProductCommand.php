<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;

class CheckAvailabilityProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:availability-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all product availability';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProductService::checkActive();
        ProductService::checkClosed();
        return Command::SUCCESS;
    }
}
