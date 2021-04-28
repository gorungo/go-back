<?php
namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
class PostCategory extends Pivot {

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    public function postCategory()
    {
        return $this->belongsTo('App\Models\PostCategory');
    }


}
