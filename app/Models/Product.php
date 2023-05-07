<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        "unit_id", 
        "product_category_id", 
        "product_type_code", 
        "code", 
        "name", 
        "description", 
        "price_list",
        "flex_price",
        "status", 
        "created_by"
    ];

    public function unit(){
        return $this->belongsTo(Unit::class, "unit_id", "id");
    }

    public function category(){
        return $this->belongsTo(ProductCategory::class, "product_category_id", "id");
    }

    public function type(){
        return $this->belongsTo(ProductType::class, "product_type_code", "code");
    }
}
