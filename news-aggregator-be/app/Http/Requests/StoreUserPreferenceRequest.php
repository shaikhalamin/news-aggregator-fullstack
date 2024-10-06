<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferenceRequest extends FormRequest
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
            'source' => 'required|string|unique:user_preferences,source|max:255',
            'metadata' => 'sometimes|required|array',
            'metadata.categories' => 'sometimes|required|array|min:1',
            'metadata.categories.*' => 'sometimes|required|string|max:255',
            'metadata.authors' => 'sometimes|required|array|min:1',
            'metadata.authors.*' => 'sometimes|required|string|max:255',
        ];
    }
}
