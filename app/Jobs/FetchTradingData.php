<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FetchTradingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Executing Python script to fetch trading data.');

            // Define the path to the Python script
            $scriptPath = base_path('scripts/api_test.py');

            // Execute the Python script
            $output = null;
            $returnVar = null;
            exec("python3 $scriptPath", $output, $returnVar);

            // Check if the script ran successfully
            if ($returnVar === 0) {
                Log::info('Python script executed successfully.');

                $data = Storage::disk('local')->get('leaderboard-data.json');

                $users = json_decode($data, true);

                // Store the data in a JSON file in the storage
                Storage::disk('local')->put('trading_data.json', json_encode($users));

                Log::info('Successfully fetched and stored trading data.');
            } else {
                Log::error('Python script execution failed with return code: ' . $returnVar);
            }
        } catch (\Throwable $th) {
            Log::error('Error in FetchTradingData job: ' . $th->getMessage());
        }
    }
}

