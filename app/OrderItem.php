<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    public $keyType = 'string';

    // public function post(){
    //     return $this->hasMany('App\Post');
    // }

    // public function comment(){
    //     return $this->hasMany('App\Comment');
    // }
}
