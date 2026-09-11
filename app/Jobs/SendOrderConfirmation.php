<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order->load('customer', 'items.product');

        Log::info('Order confirmation email simulated.', [
            'order_id' => $order->id,
            'customer_name' => $order->customer->name,
            'customer_email' => $order->customer->email,
            'grand_total' => $order->grand_total,
        ]);
    }
}
