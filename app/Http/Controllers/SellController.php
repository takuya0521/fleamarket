<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();
        return view('sell.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        // ExhibitionRequest の rules() でバリデーション済み
        $validated = $request->validated();

        // 画像保存（publicディスクに保存 → Storage::url で表示）
        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'seller_id'   => $request->user()->id,
            'name'        => $validated['name'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'condition'   => $validated['condition'],
            'image_path'  => $path, // items/xxx
        ]);

        $item->categories()->sync($validated['categories']);

        return redirect()->route('items.show', $item)->with('status', '出品しました。');
    }

    public function edit(Request $request, Item $item)
    {
        // 自分の出品だけ編集可
        if ($item->seller_id !== $request->user()->id) {
            abort(403);
        }

        // 購入済みは編集不可（任意）
        if ($item->purchase) {
            return redirect()->route('items.show', $item);
        }

        $categories = Category::query()->orderBy('name')->get();
        $selected = $item->categories()->pluck('categories.id')->all();

        return view('sell.edit', compact('item', 'categories', 'selected'));
    }

    public function update(ExhibitionRequest $request, Item $item)
    {
        if ($item->seller_id !== $request->user()->id) {
            abort(403);
        }
        if ($item->purchase) {
            return redirect()->route('items.show', $item);
        }

        // ExhibitionRequest の rules() でバリデーション済み
        $validated = $request->validated();

        // 画像更新（任意）
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('items', 'public');

            // 古い画像削除（任意）
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }

            $item->image_path = $newPath;
        }

        $item->name = $validated['name'];
        $item->brand = $validated['brand'] ?? null;
        $item->description = $validated['description'];
        $item->price = $validated['price'];
        $item->condition = $validated['condition'];
        $item->save();

        $item->categories()->sync($validated['categories']);

        return redirect()->route('items.show', $item)->with('status', '更新しました。');
    }

    public function destroy(Request $request, Item $item)
    {
        if ($item->seller_id !== $request->user()->id) {
            abort(403);
        }
        if ($item->purchase) {
            return redirect()->route('items.show', $item);
        }

        // 画像削除（任意）
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->categories()->detach();
        $item->delete();

        return redirect()->route('mypage.index')->with('status', '削除しました。');
    }
}
