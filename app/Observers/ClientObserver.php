<?php

namespace App\Observers;

use App\Models\Client;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    use HelperTrait;

    /**
     * Handle the Client "creating" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function creating(Client $client){
        $client->created_by = Auth::id();
        if(!self::hasPrivilege("ALLCLNT")) 
            $client->user_id = Auth::id();
            
        $client->code = (new Client)->makeNewCode($client->acquirer_type_code);
    }

     /**
     * Handle the Client "updating" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function updating(Client $client){
        if(!self::hasPrivilege("ALLCLNT")) 
            $client->user_id = Auth::id();
    }

    /**
     * Handle the Client "created" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function created(Client $client)
    {
        $this->saveLog("CLIENT_CREATED", $client);
    }

    /**
     * Handle the Client "updated" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function updated(Client $client)
    {
        $this->saveLog("CLIENT_UPDATED", $client);
    }

    /**
     * Handle the Client "deleting" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function deleting(Client $client)
    {
        $client->update(['deleted_by' => Auth::id()]);
    }

    /**
     * Handle the Client "deleted" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function deleted(Client $client)
    {
        $this->saveLog("CLIENT_DELETED", $client);
    }
}
 