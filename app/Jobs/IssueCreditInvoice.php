<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Invoice;
use App\Notifications\PayInvoiceNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueCreditInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client_id;
    protected $credit_amount;
    protected $payment_method;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id, $credit_amount, $payment_method)
    {
        $this->client_id = $client_id;
        $this->credit_amount = $credit_amount;
        $this->payment_method = $payment_method;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = Client::find($this->client_id);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'credit_amount' => $this->credit_amount,
            'total' => $this->credit_amount,
            'payment_method' => $this->payment_method,
        ]);

        $client->notify(new PayInvoiceNotif($invoice));
    }
}
