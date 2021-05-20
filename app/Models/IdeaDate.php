<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaDate extends Model
{
    protected $table = 'idea_dates';
    protected $guarded = [];

    protected $with = ['ideaPrice'];

    public $timestamps = false;

    public function idea(){
        return $this->belongsTo('App\Models\Idea');
    }

    public function ideaPrices(){
        return $this->hasMany('App\Models\IdeaPrice');
    }

    public function ideaPrice(){
        return $this->hasOne('App\Models\IdeaPrice')->withDefault(['price' => null, 'currency_id' => 3 ]);
    }

    public function getStartTimeAttribute()
    {
        if(isset($this->attributes['start_time'])){
            if($this->attributes['start_time'] === '00:00:00'){
                return null;
            }
            return $this->attributes['start_time'];
        }
        return null;
    }

    public function scopeInFuture($query)
    {
        return $query->where(function ($query) {
            $query->whereRaw("TO_DAYS(NOW()) <= TO_DAYS(`start_date`)");
        });
    }

}
