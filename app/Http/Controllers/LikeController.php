<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Item $item)
    {
        $user = $request->user();

        // 自分の商品にいいねは不可にするならここで弾く（要件に無いので任意）
        // if ($item->seller_id === $user->id) abort(403);

        $exists = $user->likedItems()->where('items.id', $item->id)->exists();

        if ($exists) {
            $user->likedItems()->detach($item->id);
        } else {
            $user->likedItems()->attach($item->id);
        }

        return back();
    }
}
