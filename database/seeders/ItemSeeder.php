<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者ユーザー（ダミー）
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            ['name' => '出品者太郎', 'password' => Hash::make('password123')]
        );

        // 購入者ユーザー（ダミー）
        User::firstOrCreate(
            ['email' => 'buyer@example.com'],
            ['name' => '購入者花子', 'password' => Hash::make('password123')]
        );

        $categories = Category::all();

        $items = [
            [
                'name' => 'シンプルな腕時計',
                'brand' => 'COACHTECH',
                'description' => 'シンプルで使いやすい腕時計です。',
                'price' => 5000,
                'condition' => '目立った傷や汚れなし',
                'image_path' => 'public/dummy.jpg',
                'category_names' => ['ファッション'],
            ],
            [
                'name' => 'ワイヤレスイヤホン',
                'brand' => 'SOUND',
                'description' => 'ノイズキャンセル対応のイヤホンです。',
                'price' => 7800,
                'condition' => 'やや傷や汚れあり',
                'image_path' => 'public/dummy.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => '木製サイドテーブル',
                'brand' => 'WOOD',
                'description' => '部屋に馴染む木製テーブルです。',
                'price' => 3200,
                'condition' => '新品、未使用',
                'image_path' => 'public/dummy.jpg',
                'category_names' => ['インテリア'],
            ],
            [
                'name' => 'デザイン入門書',
                'brand' => null,
                'description' => 'デザインの基礎が学べる本です。',
                'price' => 1200,
                'condition' => '目立った傷や汚れなし',
                'image_path' => 'public/dummy.jpg',
                'category_names' => ['本・音楽・ゲーム'],
            ],
        ];

        foreach ($items as $data) {
            $categoryNames = $data['category_names'];
            unset($data['category_names']);

            $item = Item::create([
                'seller_id' => $seller->id,
                ...$data,
            ]);

            $attachIds = $categories
                ->whereIn('name', $categoryNames)
                ->pluck('id')
                ->values()
                ->all();

            $item->categories()->attach($attachIds);
        }
    }
}
