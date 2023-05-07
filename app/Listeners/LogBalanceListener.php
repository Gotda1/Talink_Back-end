<?php

namespace App\Listeners;

use App\Models\Account;
use App\Models\Client;
use App\Models\LogBalance;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogBalanceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $observations = str_replace("Event", "", class_basename($event));
        Log::info($observations);
        Log::info([$event->transaction->id]);
        $transaction  = Transaction::find($event->transaction->id);
        Log::info($transaction);
        $type         = $transaction->concept->type;
        $treference   = $transaction->reference;
        $order        = $treference->modelref;
        switch (class_basename($event)) {
            case "OrderCreatedEvent":
                $this->orderLogBalance($order, $order->total, 0, $observations, $transaction->id);
                $this->clientBalance($order->client, $order->total, 0, $observations, $order->invoice, $transaction->id );
                break;
            case "PaymentEvent":
                $this->orderLogBalance($order, $transaction->amount, $type, $observations, $transaction->id);
                $this->clientBalance($order->client, $transaction->amount, $type, $observations, $order->invoice , $transaction->id);                
                $this->accountBalance($transaction->account, $transaction->amount, $type, $observations, $order->invoice , $transaction->id);
                break;
            case "RefundOrderEvent":
                $this->orderLogBalance($order, $transaction->amount, $type, $observations, $transaction->id);
                $this->clientBalance($order->client, $transaction->amount , $type, $observations, $order->invoice , $transaction->id);                
                $this->accountBalance($transaction->account, $transaction->amount, $type, $observations, $order->invoice , $transaction->id);
                break;
            case "OrderCanceledEvent":
                $this->orderLogBalance($order, $transaction->amount, 1, $observations);
                $this->clientBalance($order->client, $transaction->amount, 1, $observations, $order->invoice );
                break;                
            default:
                break;
        }
    } 


    private function orderLogBalance($order, $amount, $type, $observations, $transaction_id=0){
        LogBalance::create([
            "transaction_id" => $transaction_id,
            "entity_id"      => $order->id,
            "entity"         => Order::class,
            "amount"         => $amount,
            "balance"        => $order->status == -1 ? 0 : ($order->payed - $order->total),
            "type"           => $type,
            "invoice"        => $order->invoice,
            "observations"   => $observations
        ]);
    }
    
    private function clientBalance($client, $amount, $type, $observations, $invoice="", $transaction_id=0){
        LogBalance::create([
            "transaction_id" => $transaction_id,
            "entity_id"      => $client->id,
            "entity"         => Client::class,
            "amount"         => $amount,
            "balance"        => $client->balance,
            "type"           => $type,
            "invoice"        => $invoice,
            "observations"   => $observations
        ]);
    }

    private function accountBalance($account, $amount, $type, $observations, $invoice="", $transaction_id=0){
        LogBalance::create([
            "transaction_id" => $transaction_id,
            "entity_id"      => $account->id,
            "entity"         => Account::class,
            "amount"         => $amount,
            "balance"        => $account->balance,
            "type"           => $type,
            "invoice"        => $invoice,
            "observations"   => $observations
        ]);
    }
}
