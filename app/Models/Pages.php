<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table = 'pages';

    protected $fillable = [  'category_id', 'active' ,'city_id', 'title', 'intro' , 'description'];

}
