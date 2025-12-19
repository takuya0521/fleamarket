<?php

namespace Tests\Feature\Sell;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_sell_store_saves_item_and_category_relation(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $cat = Category::factory()->create();

        $payloadBase = [
            'name' => 'SELL_ITEM',
            'brand' => 'BRAND',
            'description' => 'DESC',
            'price' => 1000,
            'condition' => '新品',
        ];

        // カテゴリ送信の候補
        $categoryPayloads = [
            ['category_id' => $cat->id],
            ['category_ids' => [$cat->id]],
            ['categories' => [$cat->id]],
        ];

        // 画像input名の候補
        $imageKeys = ['image', 'item_image', 'image_path'];

        $passed = false;

        foreach ($categoryPayloads as $catPayload) {
            foreach ($imageKeys as $imgKey) {
                $payload = array_merge($payloadBase, $catPayload, [
                    $imgKey => UploadedFile::fake()->image('item.jpg'),
                ]);

                $res = $this->actingAs($user)->post('/sell', $payload);

                if (!in_array($res->getStatusCode(), [302, 303], true)) {
                    continue;
                }

                // item が作られているか
                $item = Item::where('name', 'SELL_ITEM')->latest('id')->first();
                if (!$item) {
                    continue;
                }

                // category_item が紐付いているか（中間テーブル直確認）
                $linked = DB::table('category_item')
                    ->where('item_id', $item->id)
                    ->where('category_id', $cat->id)
                    ->exists();

                if ($linked) {
                    $passed = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($passed, 'Sell store test failed. Check request field names for image/category in SellController.');
    }
}
