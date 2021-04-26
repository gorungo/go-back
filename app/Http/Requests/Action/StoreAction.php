<?php

namespace App\Http\Requests\Action;

use Illuminate\Foundation\Http\FormRequest;

class StoreAction extends FormRequest
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
            'relationships.idea.id' => 'required|integer',

            'relationships.places' => 'array|required',
            'relationships.places.*.id' => 'required|exists:places,id',

            'relationships.dates' => 'array|required',
            'relationships.dates.*.attributes.start_datetime_utc' => 'date',

            'attributes.title' => 'required|min:3|max:100',
            'attributes.intro' => 'required|min:3|max:199',
            'attributes.description' => 'required|min:5',
            'attributes.active' => 'required|integer',

            'relationships.price.attributes.price' => 'nullable',
            'relationships.price.relationships.currency.id' => 'required|integer|exists:currencies,id',

        ];
    }


}
