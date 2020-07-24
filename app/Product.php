<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public $keyType = 'string';

    // public function post(){
    //     return $this->hasMany('App\Post');
    // }

    // public function comment(){
    //     return $this->hasMany('App\Comment');
    // }
}
