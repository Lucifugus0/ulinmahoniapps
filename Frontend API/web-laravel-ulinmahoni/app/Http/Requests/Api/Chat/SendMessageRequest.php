<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message_text' => 'required_without:image|nullable|string|max:5000',
            'image' => 'required_without:message_text|nullable|image|mimes:jpeg,jpg,png,gif|max:10240', // 10MB
            'message_type' => 'nullable|in:text,image'
        ];
    }

    public function messages()
    {
        return [
            'message_text.required_without' => 'Either message text or image is required',
            'image.required_without' => 'Either message text or image is required',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, jpg, png, or gif format',
            'image.max' => 'Image size must not exceed 10MB',
            'message_text.max' => 'Message must not exceed 5000 characters'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
