<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Item;

class DummyItemsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 事故防止（本番でtruncateしない）
        if (!app()->environment(['local', 'testing'])) {
            $this->command?->warn('DummyItemsSeeder is only for local/testing.');
            return;
        }

        // items/users を消す前に、ぶら下がるテーブルも消す（外部キー対策）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (['likes', 'comments', 'purchases', 'category_item', 'items', 'users'] as $t) {
            if (Schema::hasTable($t)) DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 画像カラム判定（image_path or img_url）
        $imgCol = Schema::hasColumn('items', 'image_path') ? 'image_path'
               : (Schema::hasColumn('items', 'img_url') ? 'img_url' : null);

        // 出品者（佐藤売人）
        $seller = User::create([
            'name' => '佐藤売人',
            'email' => 'sato-uribito@example.com',
            'password' => Hash::make('password123'),
        ]);

        $rows = [
            ['腕時計',15000,'Rolax','スタイリッシュなデザインのメンズ腕時計','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg','良好'],
            ['HDD',5000,'西芝','高速で信頼性の高いハードディスク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg','目立った傷や汚れなし'],
            ['玉ねぎ3束',300,'なし','新鮮な玉ねぎ3束のセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg','やや傷や汚れあり'],
            ['革靴',4000,null,'クラシックなデザインの革靴','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg','状態が悪い'],
            ['ノートPC',45000,null,'高性能なノートパソコン','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg','良好'],
            ['マイク',8000,'なし','高音質のレコーディング用マイク','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg','目立った傷や汚れなし'],
            ['ショルダーバッグ',3500,null,'おしゃれなショルダーバッグ','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg','やや傷や汚れあり'],
            ['タンブラー',500,'なし','使いやすいタンブラー','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg','状態が悪い'],
            ['コーヒーミル',4000,'Starbacks','手動のコーヒーミル','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg','良好'],
            ['メイクセット',2500,null,'便利なメイクアップセット','https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg','目立った傷や汚れなし'],
        ];

        foreach ($rows as [$name,$price,$brand,$desc,$url,$cond]) {
            $data = [
                'seller_id' => $seller->id,
                'name' => $name,
                'price' => $price,
                'brand' => $brand,
                'description' => $desc,
                'condition' => $cond,
            ];
            if ($imgCol) $data[$imgCol] = $url;

            // fillable問題を回避（Model側にfillable設定してなくても入る）
            $item = new Item();
            $item->forceFill($data)->save();
        }

        $this->command?->info('Dummy items seeded: ' . Item::count());
    }
}
