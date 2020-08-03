<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coment extends Model
{
    protected $fillable =['description','photo_id','user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function photo(){
        return $this->belongsTo(Photo::class);
    }
}
