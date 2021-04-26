<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressDescription extends Model
{
    protected $table = 'address_descriptions';
    protected $guarded = [];
    public $timestamps = false;

    public function address()
    {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }
}
