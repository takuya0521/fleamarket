<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 例：カテゴリ名は要件/Figmaに合わせて後で調整OK
        $names = [
            'ファッション',
            '家電',
            'インテリア',
            '本・音楽・ゲーム',
            'スポーツ・レジャー',
            'コスメ・美容',
            'ハンドメイド',
            'その他',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
