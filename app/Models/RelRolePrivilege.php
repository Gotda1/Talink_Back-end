<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelRolePrivilege extends Model
{
    use HasFactory;

    protected $table = "rel_role_privilege";
    protected $primaryKey = null;
    
    public $incrementing = false;
    public $timestamps = false;

    public function roles () {

        return $this->belongsToMany(Role::class);
        
    }

    public function privileges () {

        return $this->belongsToMany(Privileges::class);
        
    }

}
