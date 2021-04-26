<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryDescription extends Model
{
    protected $table = 'itinerary_descriptions';

    protected $fillable = ['title', 'description', 'what_included', 'will_visit', 'locale_id'];

    protected $touches = ['itinerary'];

    public $timestamps = false;

    public function itinerary()
    {
        return $this->belongsTo('App\Models\Itinerary', 'itinerary_id');
    }

    public function locale()
    {
        return $this->belongsTo('App\Models\Locale');
    }

}
