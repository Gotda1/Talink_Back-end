<?php

namespace App\Observers;

use App\Models\Goal;
use Illuminate\Support\Facades\Auth;

class GoalListener
{
    /**
     * Handle the Goal "creating" event.
     *
     * @param  \App\Models\Goal  $goal
     * @return void
     */
    public function creating(Goal $goal)
    {
        $goal->created_by = Auth::id();
    }

    /**
     * Handle the Goal "updated" event.
     *
     * @param  \App\Models\Goal  $goal
     * @return void
     */
    public function updated(Goal $goal)
    {
        //
    }

    /**
     * Handle the Goal "deleted" event.
     *
     * @param  \App\Models\Goal  $goal
     * @return void
     */
    public function deleted(Goal $goal)
    {
        //
    }

    /**
     * Handle the Goal "restored" event.
     *
     * @param  \App\Models\Goal  $goal
     * @return void
     */
    public function restored(Goal $goal)
    {
        //
    }

    /**
     * Handle the Goal "force deleted" event.
     *
     * @param  \App\Models\Goal  $goal
     * @return void
     */
    public function forceDeleted(Goal $goal)
    {
        //
    }
}
