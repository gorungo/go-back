<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingInfo extends Model
{
    protected $table = 'booking_infos';
    protected $fillable = ['info', 'contacts', 'whatsapp'];

    public $timestamps = false;
}
