<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    public $keyType = 'string';

    // public function post(){
    //     return $this->hasMany('App\Post');
    // }

    // public function comment(){
    //     return $this->hasMany('App\Comment');
    // }
}
