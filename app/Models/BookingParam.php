<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingParam extends Model
{
    protected $table = 'booking_params';
    protected $fillable = ['info', 'contacts', 'whatsapp'];

    public $timestamps = false;
}
