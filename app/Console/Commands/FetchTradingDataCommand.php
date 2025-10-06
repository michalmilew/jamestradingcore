<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchTradingData;

class FetchTradingDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading:fetch-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch trading data from the API';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        FetchTradingData::dispatch();

        $this->info('Job dispatched to fetch trading data.');
    }
}
