<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTitle extends Model
{

    protected $table = 'category_titles';
    public $timestamps = false;
    protected $fillable = [ 'category_id', 'locale_id', 'title', 'intro'];

    protected $touches = ['category'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
