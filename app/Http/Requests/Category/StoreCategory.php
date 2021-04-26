<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategory extends FormRequest
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

            'relationships.categoryParent.id' => 'sometimes|numeric|exists:categories,id',

        ];
    }
}
