<?php

namespace App\Observers;

use App\Models\Product;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    use HelperTrait;

     /**
     * Handle the Product "creating" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function creating(Product $product){
        $product->created_by = Auth::id() ?: 0;
    }


    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        $this->saveLog("PRODUCT_CREATED", $product);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        $this->saveLog("PRODUCT_UPDATED", $product);
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        $this->saveLog("PRODUCT_DELETED", $product);
    }
}
