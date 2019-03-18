<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    //RELACION
    public  function  user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
