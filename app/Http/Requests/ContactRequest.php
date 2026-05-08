<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.max' => 'Nội dung không được vượt quá 2000 ký tự.',
        ];
    }
}
