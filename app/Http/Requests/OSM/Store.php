<?php

namespace App\Http\Requests\OSM;

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
            'display_name' => 'required|string',
            //'icon' => 'required|string',
            'importance' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'licence' => 'required',
            'osm_id' => 'required|integer',
            'osm_type' => 'required|string',
            'place_id' => 'required|integer',
            'type' => 'required|string',
        ];
    }
    public function messages()
    {
        return [];
    }
}
