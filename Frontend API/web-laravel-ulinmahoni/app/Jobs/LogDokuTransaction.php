<?php

namespace App\Jobs;

use App\Models\TransactionLogging;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogDokuTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Transaction logging data
     *
     * @var array
     */
    protected $logData;

    /**
     * Create a new job instance.
     *
     * @param array $logData
     * @return void
     */
    public function __construct(array $logData)
    {
        $this->logData = $logData;

        // Use a dedicated queue for payment logging
        $this->onQueue('payment-logging');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            TransactionLogging::create($this->logData);

            // Log::debug('DOKU Transaction Log Created Successfully', [
            //     'invoice_number' => $this->logData['invoice_number'] ?? 'N/A',
            //     'status' => $this->logData['status'] ?? 'N/A',
            //     'payment_method' => $this->logData['payment_method'] ?? 'N/A',
            // ]);
        } catch (\Exception $e) {
            // Log::error('Failed to Create DOKU Transaction Log', [
            //     'error' => $e->getMessage(),
            //     'invoice_number' => $this->logData['invoice_number'] ?? 'N/A',
            //     'attempt' => $this->attempts(),
            // ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::critical('DOKU Transaction Logging Job Failed After All Retries', [
            'error' => $exception->getMessage(),
            'invoice_number' => $this->logData['invoice_number'] ?? 'N/A',
            'payment_method' => $this->logData['payment_method'] ?? 'N/A',
            'log_data' => $this->logData,
        ]);

        // You could implement alternative logging here (e.g., file logging, external service)
        // or trigger an alert to monitor failed logging attempts
    }
}
