<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            // 「確認用パスワード」は required にしない（未入力エラー文言が要件に無いので）
            // 空でもパスワードが入っていれば same で落ちる → 「パスワードと一致しません」にできる
            'password_confirmation' => ['same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',

            'email.required' => 'メールアドレスを入力してください',
            'email.email'    => 'メールアドレスはメール形式で入力してください',

            'password.required' => 'パスワードを入力してください',
            'password.min'      => 'パスワードは8文字以上で入力してください',

            'password_confirmation.same' => 'パスワードと一致しません',
            'email.unique' => 'メールアドレスは既に使用されています',
        ];
    }
}
