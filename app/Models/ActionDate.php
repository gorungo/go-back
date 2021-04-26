<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionDate extends Model
{
    protected $table = 'action_dates';
    protected $guarded = [];

    public $timestamps = false;

    public function action(){
        return $this->belongsTo('App\Models\Action');
    }
}
