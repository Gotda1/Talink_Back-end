<?php

namespace App\Observers;

use App\Models\NoteObservation;
use Illuminate\Support\Facades\Auth;

class NoteObservationObserver
{
    /**
     * Handle the NoteObservation "creating" event.
     *
     * @param  \App\Models\NoteObservation  $noteObservation
     * @return void
     */
    public function creating(NoteObservation $noteObservation)
    {   
        $noteObservation->created_by = Auth::id();
    }
}
