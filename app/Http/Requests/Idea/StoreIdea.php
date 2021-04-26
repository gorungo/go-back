<?php

namespace App\Http\Requests\Idea;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIdea extends FormRequest
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
        $rules = [];

        if(!Auth()->user()->can('createMainIdea')){
            $rules[] = [
                'relationships.idea.id' => 'required|integer|exists:ideas,id',

                'relationships.places' => 'array|required',
                'relationships.places.*.id' => 'required|exists:places,id',

                'relationships.dates' => 'array|required',
                'relationships.dates.*.attributes.start_datetime_utc' => 'date',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'relationships.categories.required' => __('category.relationships.categories.required'),
        ];
    }

}
