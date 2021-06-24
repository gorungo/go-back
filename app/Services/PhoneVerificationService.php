<?php


namespace App\Services;


use App\Classes\Helper;
use App\Classes\SMS;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PhoneVerificationService
{

    public bool $test = false;

    public function createVerificationAndSendCode($phone)
    {
        $smsConfig = config('services.smsru');
        $newCode = rand(100000, 999999);

        if($smsConfig['active']){
            $sms = New SMS();
            $data = New \stdClass();

            $data->to = $phone;
            $data->text = "Gorungo code: " . $newCode;

            if ($this->test) {
                $data->test = 1;
            }

            $smsResult = $sms->send_one($data);

        }

        PhoneVerification::wherePhone($phone)->IsActive()->delete();
        return PhoneVerification::createVerification($phone, $newCode, $smsResult->sms_id ?? 0);
    }

}
