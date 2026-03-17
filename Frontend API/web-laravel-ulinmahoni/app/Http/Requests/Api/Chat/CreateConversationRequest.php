<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateConversationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required|string|max:100|exists:t_transactions,order_id',
            'title' => 'nullable|string|max:255',
            'initial_message' => 'nullable|string|max:5000'
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'Order ID is required',
            'order_id.exists' => 'Order ID does not exist',
            'title.max' => 'Title must not exceed 255 characters',
            'initial_message.max' => 'Initial message must not exceed 5000 characters'
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
