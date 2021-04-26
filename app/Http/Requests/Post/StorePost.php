<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePost extends FormRequest
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
            'attributes.title' => 'required|min:3|max:100',
            'attributes.intro' => 'required|min:3|max:199',
            'attributes.description' => 'required|min:5',
            'attributes.active' => 'required|integer',

            'relationships.categories' => 'required|array',
            'relationships.categories.*.id' => 'required|numeric|exists:post_categories',

        ];
    }

    public function messages()
    {
        return [
            'relationships.categories.required' => __('category.relationships.categories.required'),
        ];
    }

}
