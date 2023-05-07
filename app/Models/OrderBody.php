<?php

namespace App\Models;

use App\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBody extends Model
{
    use HasFactory, HelperTrait;

    protected $table = "orders_body";

    protected $fillable = [
        "order_id",  
        "product_id", 
        "quantity", 
        "quantity_surt", 
        "name",  
        "observations",  
        "price_list", 
        "discount", 
        "unit_price", 
        "order", 
        "created_by"
    ];

    
    public function product(){
        return $this->hasOne(Product::class, "id", "product_id");        
    }

    public function order(){
        return $this->belongsTo(Order::class, "order_id", "id");
    }
}
