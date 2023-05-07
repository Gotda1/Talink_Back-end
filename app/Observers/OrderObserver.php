<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    use HelperTrait;

    /**
     * Handle the Order "creating" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function creating(Order $order)
    {
        $client            = Client::find($order->client_id);
        $seller_id         = $client->user_id != 0 ? $client->user_id : Auth::id();
        $seller            = User::find($seller_id );
        $order->seller_id  = $seller_id;
        $order->invoice    = (new Order())->makeNewInvoice($seller);
        $order->created_by = Auth::id();
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $order = $order->with(["client","order_body"])->get();
        $this->saveLog("ORDER_CREATED", $order);    
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the Order "deleting" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleting(Order $order)
    {
        $order->update(['deleted_by' => Auth::id()]);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $order->client;
        $order->order_body;
        $this->saveLog("ORDER_DELETED", $order);   
    }
}
