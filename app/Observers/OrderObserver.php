<?php

namespace App\Observers;

use App\Models\Order;
use App\Jobs\SendOrderWebhookJob;

class OrderObserver
{
    public function created(Order $order)
    {
        // Sempre que uma SAT for criada, dispara a Job para a fila
        SendOrderWebhookJob::dispatch($order, "Abertura de SAT");
    }
}