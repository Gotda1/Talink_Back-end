<?php

namespace App\Models;

use App\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuotationBody extends Model
{
    use HasFactory, HelperTrait;   

    protected $table = "quotations_body";

    protected $fillable = [
        "quotation_id", 
        "product_id", 
        "quantity", 
        "name", 
        "price_list", 
        "discount", 
        "unit_price", 
        "order", 
        "created_by"
    ];

    public function product(){
        return $this->hasOne(Product::class, "id", "product_id");        
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class, "quotation_id", "id");
    }
}
