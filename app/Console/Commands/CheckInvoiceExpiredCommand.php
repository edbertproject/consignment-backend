<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class CheckInvoiceExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoice-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if any invoice expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        InvoiceService::checkInvoiceExpired();
        return Command::SUCCESS;
    }
}
