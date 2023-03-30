<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'mobile' => 'required|max:11|regex:/^09[0-9].{8}/',
            'password' => 'required'
        ];
    }


    public function messages(): array
    {
        $messages =  parent::messages();
        $messages['mobile.regex'] = 'phone number is invalid';
        return $messages;
    }
}
