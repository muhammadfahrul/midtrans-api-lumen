<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    // public $keyType = 'string';

    public function orderitem(){
        return $this->hasMany('App\OrderItem');
    }
}
