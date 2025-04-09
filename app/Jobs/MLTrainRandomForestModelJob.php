<?php

namespace App\Jobs;

use App\Http\Controllers\MLRandomForestController;
use App\Services\MLTrainRandomForestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MLTrainRandomForestModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Call the service to train the model
            app(MLTrainRandomForestService::class)->trainAndStoreModel();

            Log::info('ML Model training completed successfully.');
        } catch(\Exception $e) {
            Log::error('ML Model training failed: ' . $e->getMessage());
        }
    }
}
