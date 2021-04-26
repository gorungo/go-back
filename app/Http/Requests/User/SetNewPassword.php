<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\User;
use Auth;

class SetNewPassword extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'password.old' => 'required',
            'password.new' => 'required|min:6|confirmed',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if(!Auth()->user()->hasAnyRole(['admin', 'super-admin'])){
                if(!request()->has('password.old') || request()->input('password.old') == ''){
                    $validator->errors()->add('password.old.required', $this->messages()['password.old.required']);
                }else{
                    if ( !Auth::guard('web')->attempt( [
                        'email'    => request()->input('password.email'),
                        'password' => request()->input('password.old'),
                    ] )) {

                        if(!Auth()->user()->hasAnyRole(['admin', 'super-admin'])){
                            $validator->errors()->add('password.old.bad_credentials',$this->messages()['password.old.bad_credentials']);
                        }
                    }
                }
            }




        });

    }

    public function attributes()
    {

        return [
            'password.old' => 'старый пароль',
            'password.new'     => 'новый пароль',

        ];
    }

    public function messages()
    {
        return [
            'password.old.required' => 'Введите старый пароль.',
            'password.old.bad_credentials' => 'Вы ввели неправильный старый пароль.',
        ];
    }
}
