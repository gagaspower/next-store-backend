<?php

namespace App\Jobs;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pdf      = Pdf::loadView('invoice', ['data' => $this->data]);
        $filename = 'INVOICE#' . $this->data->order_code . '.pdf';
        $pdf->save(public_path('/invoice/' . $filename));
        $invoicePath = public_path('/invoice/' . $filename);

        OrderEmailJob::dispatch($this->data, $invoicePath);
    }
}
