<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emails' => 'required|array',
            'emails.*.to_email' => 'required|email',
            'emails.*.subject' => 'required|string',
            'emails.*.body' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'emails.array' => 'Invalid email format',
            'emails.*.to_email.required' => 'To email is required',
            'emails.*.subject.required' => 'Subject is required',
            'emails.*.body.required' => 'Body is required',
            'emails.*.to_email.email' => 'To email is not a valid email',
            'emails.*.subject.string' => 'Invalid type used for subject',
            'emails.*.body.string' => 'Invalid type used for body',
        ];
    }
}
