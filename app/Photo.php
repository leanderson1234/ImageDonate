<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected  $fillable = ['name','path','description','tags',"user_id"];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function coments(){
        return $this->hasMany(Coment::class);
    }
}
