<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $table = 'locales';
    protected $guarded = [];
    public $timestamps = false;

    public function address()
    {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }
}
