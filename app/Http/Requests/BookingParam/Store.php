<?php

namespace App\Http\Requests\BookingParam;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'attributes.info' => 'required|min:3|max:100',
            'attributes.contacts' => 'required|min:3|max:199',
            'attributes.whatsapp' => 'required|min:5',
        ];
    }
}
