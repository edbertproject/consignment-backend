<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;

class NotifyParticipantSpecialAuction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:participant-auction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder notification for participant who selected';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProductService::informSpecialAuctionParticipant();
        return Command::SUCCESS;
    }
}
