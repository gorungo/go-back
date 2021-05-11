<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'invite' => 'required|string|min:3|max:100',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->getInvite(request()->input('email')) === request()->input('invite')) {
                $validator->errors()->add('invite', 'Wrong invite');
            }
        });
    }

    private function getInvite($email): string
    {
        $l = strlen($email);
        return request()->email . '*' . $l*2 . $l;
    }

}
