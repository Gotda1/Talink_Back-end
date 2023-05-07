<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = "accounts";

    protected $fillable = ["name", "description", "balance", "created_by"];

    public function incrementBalance($amount){
        $this->balance += round($amount, 2); 
        $this->save();
    }

    public function decrementBalance($amount){
        $this->balance -= round($amount, 2); 
        $this->save();
    }
}
