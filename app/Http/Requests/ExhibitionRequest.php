<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 更新(sell.update) のときだけ画像を任意にする
        $isUpdate = $this->routeIs('sell.update');

        return [
            // 商品名：入力必須
            'name' => ['required', 'string', 'max:255'],

            // 商品説明：入力必須、最大255
            'description' => ['required', 'string', 'max:255'],

            // 商品画像：出品は必須 / 更新は任意、拡張子 .jpeg / .png
            'image' => array_merge(
                $isUpdate ? ['nullable'] : ['required'],
                ['file', 'mimes:jpeg,png']
            ),

            // 商品のカテゴリー：選択必須（1つ以上）
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],

            // 商品の状態：選択必須
            'condition' => ['required', 'string'],

            // 商品価格：入力必須、数値型、0円以上
            'price' => ['required', 'numeric', 'min:0'],

            // ブランド：任意（フォームにあるので一応）
            'brand' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',

            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '商品画像は.jpegまたは.png形式でアップロードしてください',

            'categories.required' => '商品のカテゴリーを選択してください',
            'categories.array' => '商品のカテゴリーを選択してください',
            'categories.min' => '商品のカテゴリーを選択してください',
            'categories.*.exists' => '商品のカテゴリーを正しく選択してください',

            'condition.required' => '商品の状態を選択してください',

            'price.required' => '商品価格を入力してください',
            'price.numeric' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
