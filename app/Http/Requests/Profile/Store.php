<?php

namespace App\Http\Requests\Profile;

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
            'data.attributes.name' => 'required|min:3|max:100',
            'data.attributes.description' => 'nullable|max:500',
            'data.attributes.site' => 'nullable|min:3|max:100',
            'data.attributes.phone' => 'nullable|min:3|max:20',
            'data.attributes.sex' => 'nullable|numeric|min:0|max:3',

        ];
    }


}
