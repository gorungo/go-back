<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategoryDescription extends Model
{
    protected $table = 'post_category_descriptions';
    public $timestamps = false;

    protected $fillable = [ 'post_category_id', 'locale_id', 'description'];

    protected $touches = ['postCategory'];

    public function postCategory()
    {
        return $this->belongsTo('App\Models\PostCategory', 'post_category_id');
    }
}
