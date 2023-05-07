<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Models\User;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuotationObserver
{
    use HelperTrait;

    /**
     * Handle the Quotation "creating" event.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return void
     */
    public function creating(Quotation $quotation)
    {
        $acquirer  = app($quotation->catalogue)->find($quotation->acquirer_id);
        $seller_id = $acquirer->user_id != 0 ? $acquirer->user_id : Auth::id();
        $seller    = User::find($seller_id);

        $quotation->seller_id  = $seller_id;
        $quotation->invoice    = (new Quotation)->makeNewInvoice($seller);
        $quotation->order_id   = 0;
        $quotation->status     = 0;
        $quotation->created_by = Auth::user()->id;
    }
    
    /**
     * Handle the Quotation "created" event.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return void
     */
    public function created(Quotation $quotation)
    {
        $quotation = $quotation->with(["acquirer","quot_body"])->get();
        $this->saveLog("QUOTATION_CREATED", $quotation);        
    }

    /**
     * Handle the Quotation "updated" event.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return void
     */
    public function updated(Quotation $quotation)
    {
        $this->saveLog("QUOTATION_UPDATED", $quotation);    
    }

    /**
     * Handle the Quotation "deleting" event.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return void
     */
    public function deleting(Quotation $quotation)
    {
        $quotation->acquirer;
        $quotation->quot_body;
        $quotation->update(['deleted_by' => Auth::id()]);
        $this->saveLog("QUOTATION_DELETED", $quotation);    
    }
}
  