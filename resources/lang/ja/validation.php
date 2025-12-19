<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute を承認してください。',
    'accepted_if' => ':other が :value の場合、:attribute を承認してください。',
    'active_url' => ':attribute は、有効なURLではありません。',
    'after' => ':attribute は、:date より後の日付にしてください。',
    'after_or_equal' => ':attribute は、:date 以降の日付にしてください。',
    'alpha' => ':attribute は、アルファベットのみが利用できます。',
    'alpha_dash' => ':attribute は、アルファベット、数字、ハイフン、アンダースコアのみが利用できます。',
    'alpha_num' => ':attribute は、アルファベットと数字のみが利用できます。',
    'array' => ':attribute は、配列でなくてはなりません。',
    'ascii' => ':attribute は、英数字及び記号のみが利用できます。',
    'before' => ':attribute は、:date より前の日付にしてください。',
    'before_or_equal' => ':attribute は、:date 以前の日付にしてください。',
    'between' => [
        'array' => ':attribute は、:min から :max 個の間で指定してください。',
        'file' => ':attribute は、:min から :max KBの間で指定してください。',
        'numeric' => ':attribute は、:min から :max の間で指定してください。',
        'string' => ':attribute は、:min から :max 文字の間で指定してください。',
    ],
    'boolean' => ':attribute は、true か false を指定してください。',
    'confirmed' => ':attribute と一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付ではありません。',
    'date_equals' => ':attribute は :date と同じ日付にしてください。',
    'date_format' => ':attribute の形式は :format で入力してください。',
    'decimal' => ':attribute は :decimal 桁の小数で入力してください。',
    'declined' => ':attribute は拒否してください。',
    'declined_if' => ':other が :value の場合、:attribute は拒否してください。',
    'different' => ':attribute と :other は異なる値を入力してください。',
    'digits' => ':attribute は :digits 桁で入力してください。',
    'digits_between' => ':attribute は :min から :max 桁で入力してください。',
    'dimensions' => ':attribute の画像サイズが無効です。',
    'distinct' => ':attribute に重複した値があります。',
    'doesnt_end_with' => ':attribute の末尾は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attribute の先頭は次のいずれかで始まってはいけません: :values。',
    'email' => ':attribute はメール形式で入力してください。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute は正しくありません。',
    'exists' => '選択された :attribute は正しくありません。',
    'extensions' => ':attribute は次の拡張子を指定してください: :values。',
    'file' => ':attribute はファイルにしてください。',
    'filled' => ':attribute は必須です。',
    'gt' => [
        'array' => ':attribute は :value 個より多く指定してください。',
        'file' => ':attribute は :value KBより大きく指定してください。',
        'numeric' => ':attribute は :value より大きく指定してください。',
        'string' => ':attribute は :value 文字より多く指定してください。',
    ],
    'gte' => [
        'array' => ':attribute は :value 個以上指定してください。',
        'file' => ':attribute は :value KB以上で指定してください。',
        'numeric' => ':attribute は :value 以上で指定してください。',
        'string' => ':attribute は :value 文字以上で指定してください。',
    ],
    'image' => ':attribute は画像にしてください。',
    'in' => '選択された :attribute は正しくありません。',
    'in_array' => ':attribute は :other に存在しません。',
    'integer' => ':attribute は整数で入力してください。',
    'ip' => ':attribute は有効なIPアドレスで入力してください。',
    'ipv4' => ':attribute は有効なIPv4アドレスで入力してください。',
    'ipv6' => ':attribute は有効なIPv6アドレスで入力してください。',
    'json' => ':attribute は有効なJSON文字列で入力してください。',
    'lowercase' => ':attribute は小文字で入力してください。',
    'lt' => [
        'array' => ':attribute は :value 個より少なく指定してください。',
        'file' => ':attribute は :value KBより小さく指定してください。',
        'numeric' => ':attribute は :value より小さく指定してください。',
        'string' => ':attribute は :value 文字より少なく指定してください。',
    ],
    'lte' => [
        'array' => ':attribute は :value 個以下で指定してください。',
        'file' => ':attribute は :value KB以下で指定してください。',
        'numeric' => ':attribute は :value 以下で指定してください。',
        'string' => ':attribute は :value 文字以下で指定してください。',
    ],
    'mac_address' => ':attribute は有効なMACアドレスで入力してください。',
    'max' => [
        'array' => ':attribute は :max 個以下で指定してください。',
        'file' => ':attribute は :max KB以下で指定してください。',
        'numeric' => ':attribute は :max 以下で指定してください。',
        'string' => ':attribute は :max 文字以下で指定してください。',
    ],
    'max_digits' => ':attribute は :max 桁以下で入力してください。',
    'mimes' => ':attribute は :values タイプのファイルで指定してください。',
    'mimetypes' => ':attribute は :values タイプのファイルで指定してください。',
    'min' => [
        'array' => ':attribute は :min 個以上で指定してください。',
        'file' => ':attribute は :min KB以上で指定してください。',
        'numeric' => ':attribute は :min 以上で指定してください。',
        'string' => ':attribute は :min 文字以上で指定してください。',
    ],
    'min_digits' => ':attribute は :min 桁以上で入力してください。',
    'missing' => ':attribute は存在してはいけません。',
    'missing_if' => ':other が :value の場合、:attribute は存在してはいけません。',
    'missing_unless' => ':other が :value でない場合、:attribute は存在してはいけません。',
    'missing_with' => ':values が存在する場合、:attribute は存在してはいけません。',
    'missing_with_all' => ':values が存在する場合、:attribute は存在してはいけません。',
    'multiple_of' => ':attribute は :value の倍数で入力してください。',
    'not_in' => '選択された :attribute は正しくありません。',
    'not_regex' => ':attribute の形式が正しくありません。',
    'numeric' => ':attribute は数字で入力してください。',
    'password' => [
        'letters' => ':attribute には文字を含めてください。',
        'mixed' => ':attribute には大文字と小文字を含めてください。',
        'numbers' => ':attribute には数字を含めてください。',
        'symbols' => ':attribute には記号を含めてください。',
        'uncompromised' => ':attribute は漏洩している可能性があります。別のものを選択してください。',
    ],
    'present' => ':attribute が存在していなければなりません。',
    'prohibited' => ':attribute は入力禁止です。',
    'prohibited_if' => ':other が :value の場合、:attribute は入力禁止です。',
    'prohibited_unless' => ':other が :values の場合、:attribute は入力禁止です。',
    'prohibits' => ':attribute は :other の入力を禁じています。',
    'regex' => ':attribute の形式が正しくありません。',
    'required' => ':attribute を入力してください。',
    'required_array_keys' => ':attribute には次のキーが含まれている必要があります: :values。',
    'required_if' => ':other が :value の場合、:attribute を入力してください。',
    'required_if_accepted' => ':other を承認した場合、:attribute を入力してください。',
    'required_unless' => ':other が :values でない場合、:attribute を入力してください。',
    'required_with' => ':values が存在する場合、:attribute を入力してください。',
    'required_with_all' => ':values が存在する場合、:attribute を入力してください。',
    'required_without' => ':values が存在しない場合、:attribute を入力してください。',
    'required_without_all' => ':values が存在しない場合、:attribute を入力してください。',
    'same' => ':attribute と :other が一致しません。',
    'size' => [
        'array' => ':attribute は :size 個で指定してください。',
        'file' => ':attribute は :size KBで指定してください。',
        'numeric' => ':attribute は :size で指定してください。',
        'string' => ':attribute は :size 文字で指定してください。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります: :values。',
    'string' => ':attribute は文字列で入力してください。',
    'timezone' => ':attribute は有効なタイムゾーンで入力してください。',
    'unique' => ':attribute はすでに存在しています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'uppercase' => ':attribute は大文字で入力してください。',
    'url' => ':attribute は有効なURLで入力してください。',
    'ulid' => ':attribute は有効なULIDで入力してください。',
    'uuid' => ':attribute は有効なUUIDで入力してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    | ここが今回の評価文言を固定する場所
    */

    'custom' => [
        'name' => [
            'required' => 'お名前を入力してください',
        ],
        'email' => [
            'required' => 'メールアドレスを入力してください',
            'email'    => 'メールアドレスはメール形式で入力してください',
        ],
        'password' => [
            'required'  => 'パスワードを入力してください',
            'min'       => 'パスワードは8文字以上で入力してください',
            'confirmed' => 'パスワードと一致しません',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
    ],
];
