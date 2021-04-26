<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceDescription extends Model
{
    protected $table = 'place_descriptions';

    protected $fillable = ['locale_id', 'title', 'intro', 'description'];

    protected $touches = [];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }
}

