<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $keyType = 'string';

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function orderitem(){
        return $this->hasMany('App\OrderItem');
    }

    public function payment(){
        return $this->hasMany('App\Payment');
    }
}
