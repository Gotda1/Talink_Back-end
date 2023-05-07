<?php

namespace App\Observers;

use App\Models\LogBalance;
use Illuminate\Support\Facades\Auth;

class LogBalanceObserver
{
       /**
     * Handle the LogBalance "creating" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function creating(LogBalance $logBalance)
    {
        $logBalance->created_by = Auth::id();
        $logBalance->created_at = $logBalance->freshTimestamp();
    }

    
    /**
     * Handle the LogBalance "created" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function created(LogBalance $logBalance)
    {
        // 
    }

    /**
     * Handle the LogBalance "updated" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function updated(LogBalance $logBalance)
    {
        //
    }

    /**
     * Handle the LogBalance "deleted" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function deleted(LogBalance $logBalance)
    {
        //
    }

    /**
     * Handle the LogBalance "restored" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function restored(LogBalance $logBalance)
    {
        //
    }

    /**
     * Handle the LogBalance "force deleted" event.
     *
     * @param  \App\Models\LogBalance  $logBalance
     * @return void
     */
    public function forceDeleted(LogBalance $logBalance)
    {
        //
    }
}
