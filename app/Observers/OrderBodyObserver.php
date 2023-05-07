<?php

namespace App\Observers;

use App\Models\OrderBody;
use Illuminate\Support\Facades\Auth;

class OrderBodyObserver
{
    /**
     * Handle the OrderBody "creating" event.
     *
     * @param  \App\Models\OrderBody  $orderBody
     * @return void
     */
    public function creating(OrderBody $orderBody)
    {
        $orderBody->created_by = Auth::id();
    }
}
