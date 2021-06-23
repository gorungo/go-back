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
        if(!$smsConfig['active']) return false;

        $newCode = rand(100000, 999999);

        //устанавливаем срок проверки смс в 2 минуты
        $exp_date = date('Y-m-d H:i:s', strtotime('2 minute'));

        $sms = New SMS();
        $data = New \stdClass();

        $data->to = $phone;
        $data->text = "Gorungo code: " . $newCode;

        if ($this->test) {
            $data->test = 1;
        }

        PhoneVerification::wherePhone($phone)->IsActive()->delete();

        if (App::environment('production')) {
            $smsResult = $sms->send_one($data);
        }

        return PhoneVerification::createVerification($phone, $newCode, $smsResult->sms_id ?? null);
    }

}
