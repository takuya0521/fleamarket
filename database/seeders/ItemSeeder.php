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

        $items = [
            [
                'name' => '腕時計',
                'brand' => null,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'condition' => '良好',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'category_names' => ['ファッション'],
            ],
            [
                'name' => 'HDD',
                'brand' => null,
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition' => '目立った傷や汚れなし',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'condition' => 'やや傷や汚れあり',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'category_names' => ['食品'],
            ],
            [
                'name' => '革靴',
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'condition' => '状態が悪い',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'category_names' => ['ファッション'],
            ],
            [
                'name' => 'ノートPC',
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition' => '良好',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition' => '目立った傷や汚れなし',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition' => 'やや傷や汚れあり',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'category_names' => ['ファッション'],
            ],
            [
                'name' => 'タンブラー',
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition' => '状態が悪い',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'category_names' => ['インテリア'],
            ],
            [
                'name' => 'コーヒーミル',
                'brand' => null,
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition' => '良好',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'category_names' => ['インテリア'],
            ],
            [
                'name' => 'メイクセット',
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition' => '目立った傷や汚れなし',
                'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'category_names' => ['ファッション'],
            ],
        ];

        foreach ($items as $data) {
            $categoryNames = $data['category_names'];
            unset($data['category_names']);

            $item = Item::create([
                'seller_id' => $seller->id,
                ...$data,
            ]);

            // Seeder側でカテゴリが存在しない場合もあるので、無ければ作ってから紐付ける
            $attachIds = collect($categoryNames)
                ->map(fn ($name) => Category::firstOrCreate(['name' => $name])->id)
                ->all();

            $item->categories()->attach($attachIds);
        }
    }
}