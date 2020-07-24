<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $keyType = 'string';

    // public function post(){
    //     return $this->hasMany('App\Post');
    // }

    // public function comment(){
    //     return $this->hasMany('App\Comment');
    // }
}
