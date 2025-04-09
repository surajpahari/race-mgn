<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddAgeGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['required', 'integer', 'min:13'], // greater than 12
            'to' => ['required', 'integer', 'max:79'], // less than 80
            'name' => ['required', 'string', 'max:100'],
        ];
    }
}
