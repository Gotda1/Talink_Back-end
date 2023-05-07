<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;    

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_code',
        'code',
        'name',
        'birthday',
        'description',
        'status',
        'email',
        'phone',
        'password', 
        'created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function prospects(){
        return $this->hasMany(Prospect::class, "user_id", "id")
                    ->orWhere("user_id",0);
    }

    public function all_prospects(){
        return $this->hasMany(Prospect::class, "user_id", "id")
                    ->orWhere("user_id",0);
    }

    public function clients(){
        return $this->hasMany(Client::class, "user_id", "id");
    }

    public function all_clients(){
        return $this->hasMany(Client::class, "user_id", "id")
                    ->orWhere("user_id",0);
    }


     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ["id" => $this->id];
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

}
