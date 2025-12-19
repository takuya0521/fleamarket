<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (request()->boolean('verified') && auth()->check()) {
            return redirect()->route('profile.edit');
        }
        $tab = $request->query('tab', 'all');           // all | mylist
        $keyword = trim((string) $request->query('keyword', ''));

        // ベースクエリ
        if ($tab === 'mylist') {
            // 未ログインなら空表示（要件）
            if (!auth()->check()) {
                $items = collect();
                return view('items.index', compact('items', 'tab', 'keyword'));
            }

            // いいねした商品
            $query = auth()->user()
                ->likedItems()
                ->with(['purchase'])
                ->withCount(['likes', 'comments']);
        } else {
            // 全商品
            $query = Item::query()
                ->with(['purchase'])
                ->withCount(['likes', 'comments']);
        }

        // 自分が出品した商品は表示しない（要件）
        if (auth()->check()) {
            $query->where('seller_id', '!=', auth()->id());
        }

        // 検索（商品名 部分一致）— tab切替でも保持（要件）
        if ($keyword !== '') {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // 並び順は新しい順（好みで変更OK）
        $items = $query->latest()->paginate(12)->withQueryString();

        $likedIds = [];

        if (auth()->check() && !($items instanceof \Illuminate\Support\Collection)) {
            $ids = $items->getCollection()->pluck('id')->all();
            $likedIds = auth()->user()
                ->likedItems()
                ->whereIn('items.id', $ids)
                ->pluck('items.id')
                ->all();
        }

        return view('items.index', compact('items', 'tab', 'keyword', 'likedIds'));

        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    // 後で実装する商品詳細用（今はダミー）
    public function show(Item $item)
    {
        $item->load([
            'categories',
            'purchase',
            'comments.user',
        ])->loadCount(['likes', 'comments']);

        $liked = auth()->check()
            ? auth()->user()->likedItems()->where('items.id', $item->id)->exists()
            : false;

        return view('items.show', compact('item', 'liked'));
    }
}
