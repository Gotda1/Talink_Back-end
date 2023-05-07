<?php

namespace App\Observers;

use App\Models\TransactionReference;

class TransactionReferenceObserver
{
    /**
     * Handle the TransactionReference "creating" event.
     *
     * @param  \App\Models\TransactionReference  $transactionReference
     * @return void
     */
    public function creating(TransactionReference $transactionReference)
    {
        $transactionReference->created_at = $transactionReference->freshTimestamp();
    }
}
