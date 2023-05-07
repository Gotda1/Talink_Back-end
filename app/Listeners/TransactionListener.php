<?php

namespace App\Listeners;

use App\Models\Client;
use App\Models\LogBalance;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionReference;
use Illuminate\Support\Facades\Log;

class TransactionListener
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {        
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // $transaction = Transaction::create([
        //     "account_id"          => $event->account_id,
        //     "concept_id"          => $event->concept_id,
        //     "amount"              => $event->amount,
        //     "payment_method_code" => $event->payment_method_code,
        //     "observations"        => $event->observations
        // ]);

        // switch (class_basename($event->model)) {
        //     case "Order":
        //         TransactionReference::create([
        //             "transaction_id" => $transaction->id,
        //             "reference_id"   => $event->model->id,
        //             "subject_id"     => $event->model->client_id,
        //             "reference_type" => Order::class,
        //             "subject_type"   => Client::class,
        //             "invoice"        => $event->model->invoice
        //         ]); 
                
        //         $event->model->setPayment($event->amount);
        //         break;
            
        //     default:
        //         break;
        // }
        
        
        
        //LogBalance::create($dataTransaction);
    } 
}
