<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ForgotPasswordRequest extends FormRequest{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Không tìm thấy tài khoản với email này',
        ];
    }
}
