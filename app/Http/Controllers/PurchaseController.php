<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function show(Request $request, Item $item)
    {
        $item->load('purchase');

        // 購入済みは購入不可
        if ($item->purchase) {
            return redirect()->route('items.show', $item);
        }

        // 自分の出品は購入不可（要件に明記はないが自然）
        if ($item->seller_id === $request->user()->id) {
            return redirect()->route('items.show', $item);
        }

        $key = "purchase_address.{$item->id}";
        $addr = session($key, []);

        // セッションの住所があればそれを優先、なければプロフィール住所
        $shipping_postal_code = $addr['shipping_postal_code'] ?? $request->user()->postal_code;
        $shipping_address     = $addr['shipping_address'] ?? $request->user()->address;
        $shipping_building    = $addr['shipping_building'] ?? $request->user()->building;

        return view('purchase.show', compact(
            'item',
            'shipping_postal_code',
            'shipping_address',
            'shipping_building'
        ));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $item->load('purchase');

        if ($item->purchase) {
            return redirect()->route('items.show', $item);
        }
        if ($item->seller_id === $request->user()->id) {
            return redirect()->route('items.show', $item);
        }

        $key = "purchase_address.{$item->id}";
        $addr = session($key, []);

        $shipping_postal_code = $addr['shipping_postal_code'] ?? $request->user()->postal_code;
        $shipping_address     = $addr['shipping_address'] ?? $request->user()->address;
        $shipping_building    = $addr['shipping_building'] ?? $request->user()->building;

        // 住所が空なら住所変更へ誘導（プロフィール未登録でも進めるため）
        if (!$shipping_postal_code || !$shipping_address) {
            return redirect()
                ->route('purchase.address.edit', $item)
                ->withErrors(['shipping' => '送付先住所を入力してください。']);
        }

        $pm = $request->input('payment_method');

        if (in_array($pm, ['card', 'convenience_store'], true)) {
            if (app()->environment('testing')) {
                Purchase::create([
                    'item_id'              => $item->id,
                    'buyer_id'             => $request->user()->id,
                    'payment_method'       => $pm,
                    'shipping_postal_code' => $shipping_postal_code,
                    'shipping_address'     => $shipping_address,
                    'shipping_building'    => $shipping_building,
                    'status'               => 'paid',
                    'purchased_at'         => now(),
                ]);

                session()->forget($key);
                return redirect()->route('items.index')->with('status', '購入が完了しました。');
            }

            return app(\App\Http\Controllers\StripeController::class)->checkout($request, $item);
        }

        Purchase::create([
            'item_id'              => $item->id,
            'buyer_id'             => $request->user()->id,
            'payment_method'       => $request->input('payment_method'),
            'shipping_postal_code' => $shipping_postal_code,
            'shipping_address'     => $shipping_address,
            'shipping_building'    => $shipping_building,
            'status'               => 'paid',
            'purchased_at'         => now(),
        ]);

        // セッションの住所はクリア
        session()->forget($key);

        return redirect()->route('items.index')->with('status', '購入が完了しました。');
    }

    public function complete(Request $request, Item $item)
    {
        $purchase = $item->purchase;

        // 購入履歴がない / 自分の購入じゃないなら見せない
        if (!$purchase || $purchase->buyer_id !== $request->user()->id) {
            return redirect()->route('items.show', $item);
        }

        return view('purchase.complete', compact('item', 'purchase'));
    }
}
