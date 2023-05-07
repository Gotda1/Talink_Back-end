<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        "year_numb",
        "month_numb",
        "amount",
        "created_by"
    ];

    public function months_goals(){
        return $this->hasMany(Goal::class, "year_numb", "year_numb");
    }
}