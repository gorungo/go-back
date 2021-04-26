<?php

namespace App\Http\Requests\Idea;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PublishIdea extends FormRequest
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
        $rules = [
            'attributes.title' => 'required|min:3|max:100',
            'attributes.intro' => 'required|min:3|max:199',
            'attributes.description' => 'required|min:5',
            'attributes.active' => 'required|integer',

            'relationships.categories' => 'required|array',
            'relationships.categories.*.id' => 'required|numeric|exists:categories,id',

            'relationships.itineraries' => Rule::requiredIf(function(){
                return request()->input('attributes.created_at') !== null;
            }),
            'relationships.itineraries.*.attributes.title' => 'required',
            'relationships.itineraries.*.attributes.description' => 'required',

            'relationships.dates' => 'required|array|nullable',
            'relationships.dates.*.attributes.start_date' => 'required|date',
            'relationships.dates.*.attributes.start_time' => 'required|min:8|max:8',
            'relationships.dates.*.relationships.ideaPrice.attributes.price' => 'required|nullable',
            'relationships.dates.*.relationships.ideaPrice.relationships.currency.id' => 'required|integer|exists:currencies,id',

        ];




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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException( response()->json([
            'errors' => $validator->errors()
        ], 200));
    }

}
