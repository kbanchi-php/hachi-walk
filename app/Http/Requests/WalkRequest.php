<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalkRequest extends FormRequest
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
        $rule = [
            'title' => 'required|max:50',
            'description' => 'max:2000',
        ];

        return $rule;
    }
}
