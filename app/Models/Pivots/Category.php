<?php
namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
class Category extends Pivot {

    public function idea()
    {
        return $this->belongsTo('App\Models\Idea');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function actions()
    {
        return $this->hasManyThrough('App\Models\Action', 'App\Models\Idea');
    }

}
