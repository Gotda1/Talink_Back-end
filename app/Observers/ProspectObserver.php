<?php

namespace App\Observers;

use App\Models\Prospect;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProspectObserver
{
    use HelperTrait;

    /**
     * Handle the Prospect "creating" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function creating(Prospect $prospect){
        $prospect->created_by = Auth::id() ?: 0;
        if(!self::hasPrivilege("ALLPRSP")) 
            $prospect->user_id = Auth::id() ?: 0;
    }

     /**
     * Handle the Prospect "updating" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function updating(Prospect $prospect){
        if(!self::hasPrivilege("ALLPRSP"))
            $prospect->user_id = Auth::id();
    }

    /**
     * Handle the Prospect "created" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function created(Prospect $prospect)
    {
        $this->saveLog("PROSPECT_CREATED", $prospect);
    } 

    /**
     * Handle the Prospect "updated" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function updated(Prospect $prospect)
    {
        $this->saveLog("PROSPECT_UPDATED", $prospect);
    }

    /**
     * Handle the Prospect "deleting" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function deleting(Prospect $prospect)
    {
        $prospect->update(['deleted_by' => Auth::id()]);
    }

    /**
     * Handle the Prospect "deleted" event.
     *
     * @param  \App\Models\Prospect  $prospect
     * @return void
     */
    public function deleted(Prospect $prospect)
    {
        $this->saveLog("PROSPECT_DELETED", $prospect);
    }
}
