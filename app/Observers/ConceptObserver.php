<?php

namespace App\Observers;

use App\Models\Concept;

class ConceptObserver
{
    /**
     * Handle the Concept "created" event.
     *
     * @param  \App\Models\Concept  $concept
     * @return void
     */
    public function created(Concept $concept)
    {
        //
    }

    /**
     * Handle the Concept "updated" event.
     *
     * @param  \App\Models\Concept  $concept
     * @return void
     */
    public function updated(Concept $concept)
    {
        //
    }

    /**
     * Handle the Concept "deleted" event.
     *
     * @param  \App\Models\Concept  $concept
     * @return void
     */
    public function deleted(Concept $concept)
    {
        //
    }

    /**
     * Handle the Concept "restored" event.
     *
     * @param  \App\Models\Concept  $concept
     * @return void
     */
    public function restored(Concept $concept)
    {
        //
    }

    /**
     * Handle the Concept "force deleted" event.
     *
     * @param  \App\Models\Concept  $concept
     * @return void
     */
    public function forceDeleted(Concept $concept)
    {
        //
    }
}
