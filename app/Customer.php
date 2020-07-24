<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    public $keyType = 'string';

    // public function post(){
    //     return $this->hasMany('App\Post');
    // }

    // public function comment(){
    //     return $this->hasMany('App\Comment');
    // }
}
