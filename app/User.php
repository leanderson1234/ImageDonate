<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    
    use Notifiable;

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
        return [];
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',"image_path"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getResults($totalPage,$data){
        if(!isset($data['filter']) && !isset($data['name']))
        return $this->paginate($totalPage);

       return  $this->where(function($query) use ($data){
            if(isset($data['filter'])){
                $filter = $data['filter'];
                $query->where('name','LIKE',"%{$filter}%");
            }
            if(isset($data['name']) )
                $query->where('name',$data['name']);
         })->paginate($totalPage);
    }


    public function photos(){
        return $this->hasMany(Photo::class);
    }
    public function coments(){
        return $this->hasMany(Coment::class);
    }
}
