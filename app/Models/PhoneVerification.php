<?php

namespace App\Models;

use App\Classes\SMS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Classes\Helper;


class PhoneVerification extends Model
{
    protected $table = 'phone_verifications';
    public $timestamps = false;
    protected $fillable = ['phone', 'code', 'exp_date', 'sms_id'];

    private $maxAttemptsCount = 3;
    const codeFreshTime = 2; // время на проверку кода в минутах

    public static function createVerification($phone, $code, $smsId)
    {
        return self::create([
            'phone' => Helper::clearPhone($phone),
            'code' => $code,
            'sms_id' => $smsId,
            'attempts' => 0,
            'exp_date' => date('Y-m-d H:i:s', strtotime(self::codeFreshTime . ' minute')),
        ]);
    }

    public function checkCode($phone, $code) : bool
    {

        if($this->attempts === $this->maxAttemptsCount){
            return false;
        }

        if ($this->phone == Helper::clearPhone($phone) && $this->code === (int)$code) {
            return true;
        } else {
            $this->attempts = $this->attempts + 1;
            $this->save();
            return false;

        }
    }

    public function scopeIsActive($query)
    {
        $now = date('Y-m-d H:i:s');
        return $query->where('exp_date', '>', $now)->where('attempts', '<=', $this->maxAttemptsCount);
    }

    public function scopeHasCode($query, $code)
    {
        return $query->where('code', $code)->where('attempts', '<=', $this->maxAttemptsCount);
    }

    public function scopeHasPhone($query, $phone)
    {
        return $query->where('phone', Helper::clearPhone($phone));
    }

    public function scopeIsNotActive($query)
    {
        $now = date('Y-m-d H:i:s');
        return $query->where('exp_date', '<=', $now);
    }

    public function scopeHasActiveCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function delete_hash()
    {

    }
}
