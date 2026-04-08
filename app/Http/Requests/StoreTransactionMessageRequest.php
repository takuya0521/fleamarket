<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTransactionMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 1. 本文：入力必須、最大文字数400
            'body' => ['required', 'string', 'max:400'],

            // 2. 画像：拡張子が .jpeg もしくは .png（mimeで絞る + 後段で拡張子もチェック）
            'image' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png'],
        ];
    }

    public function messages(): array
    {
        return [
            // 1) 本文が未入力の場合
            'body.required' => '本文を入力してください',

            // 3) 本文が401文字以上の場合
            'body.max' => '本文は400文字以内で入力してください',

            // 2) 画像が.pngまたは.jpeg形式以外の場合（文言を統一）
            'image.file' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.mimetypes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }

    /**
     * 仕様どおり「拡張子が .jpeg もしくは .png」のみ許可する
     * （mimeだけだと .jpg が通ることがあるので、拡張子もチェック）
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('image');
            if (!$file) {
                return;
            }

            $ext = strtolower((string) $file->getClientOriginalExtension());
            if (!in_array($ext, ['jpeg', 'png'], true)) {
                $validator->errors()->add('image', '「.png」または「.jpeg」形式でアップロードしてください');
            }
        });
    }
}