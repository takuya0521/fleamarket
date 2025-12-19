<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 購入した商品（PG11）
        $purchasedItems = Item::query()
            ->whereHas('purchase', fn ($q) => $q->where('buyer_id', $user->id))
            ->with(['purchase'])
            ->latest()
            ->paginate(12, ['*'], 'purchased_page');

        // 出品した商品（PG12）
        $sellingItems = Item::query()
            ->where('seller_id', $user->id)
            ->with(['purchase'])
            ->latest()
            ->paginate(12, ['*'], 'selling_page');

        return view('mypage.index', compact('user', 'purchasedItems', 'sellingItems'));
    }
}
