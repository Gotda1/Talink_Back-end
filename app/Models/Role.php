<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    protected $keyType = 'string';
    
    public $incrementing = false;
    public $timestamps = false;


    public function privileges()
    {
        return $this->hasManyThrough(Privilege::class, RelRolePrivilege::class, "role_code", "code", "code", "privilege_code");
    }
}
