<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileBooking extends Model
{
    protected $table = 'booking_infos';
    protected $fillable = ['info', 'contacts'];

    public $timestamps = false;
}
