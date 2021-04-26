<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionDescription extends Model
{
    protected $table = 'action_descriptions';

    protected $fillable = ['locale_id', 'title', 'intro', 'description'];

    protected $touches = ['action'];

    public $timestamps = false;

    public function action()
    {
        return $this->belongsTo('App\Models\Action', 'action_id');
    }
}
