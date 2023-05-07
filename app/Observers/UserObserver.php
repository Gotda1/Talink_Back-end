<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    use HelperTrait;

    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user){
        $user->created_by = Auth::id() ?: 0;
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->saveLog("USER_CREATED", $user);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $this->saveLog("USER_CREATED", $user);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->saveLog("USER_CREATED", $user);
    }
}
