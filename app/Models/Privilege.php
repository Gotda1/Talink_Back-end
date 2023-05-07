<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    
    public $incrementing = false;
    public $timestamps = false;

    public function rel_rol_privileges () {

        return $this->hasMany(RelRolPrivilege::class, 'privilege_code');
        
    }

}
