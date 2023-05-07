<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Models\QuotationBody;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;

class QuotationBodyObserver
{
    use HelperTrait;
    
    /**
     * Handle the Quotation "creating" event.
     *
     * @param  \App\Models\QuotationBody  $quotationBody
     * @return void
     */
    public function creating(QuotationBody $quotationBody)
    {
        $quotationBody->created_by = Auth::user()->id;
    }
}
