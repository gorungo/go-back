<?php


namespace App\Services;

use App\Classes\SMS;
use App\Models\PhoneVerification;
use App\Http\Resources\PhoneVerification  as PhoneVerificationResource;
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

            if ($smsConfig['test']) {
                $data->test = 1;
            }

            $smsResult = $sms->send_one($data);

            PhoneVerification::wherePhone($phone)->IsActive()->delete();
            return new PhoneVerificationResource(PhoneVerification::createVerification($phone, $newCode, $smsResult->sms_id ?? 0));
        }

        return false;

    }

    public function checkVerificationCode($data)
    {
        $phoneVerification = PhoneVerification::wherePhone($data['phone'])->isActive()->first();
        if(!$phoneVerification) return false;

        if($phoneVerification){
            return $phoneVerification->checkCode($data['code']);
        }

    }


}
