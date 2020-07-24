<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    public $keyType = 'string';

    public function order(){
        return $this->hasMany('App\Order');
    }
}
