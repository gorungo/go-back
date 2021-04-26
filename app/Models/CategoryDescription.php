<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model
{
    protected $table = 'category_descriptions';
    public $timestamps = false;

    protected $fillable = [ 'category_id', 'locale_id', 'description'];

    protected $touches = ['category'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
