<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class StripeController extends Controller
{
    public function checkout(Request $request, Item $item)
    {
        $item->load('purchase');

        if ($item->purchase) return redirect()->route('items.show', $item);
        if ($item->seller_id === $request->user()->id) return redirect()->route('items.show', $item);

        // payment_method: 'card' or 'convenience_store'
        $pm = $request->input('payment_method');
        $stripePm = $pm === 'convenience_store' ? 'konbini' : 'card';

        Stripe::setApiKey(config('services.stripe.secret'));

        session()->put('stripe_item_id', $item->id);
        session()->put('stripe_pm', $pm);

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => [$stripePm],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int) $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
            ]],
            'success_url' => route('stripe.success', $item) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.cancel',  $item),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, Item $item)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('items.show', $item);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $checkout = \Stripe\Checkout\Session::retrieve($sessionId);

        if (($checkout->payment_status ?? null) !== 'paid') {
            return redirect()
                ->route('purchase.show', $item)
                ->withErrors(['payment' => '決済が完了していません。']);
        }

        $item->load('purchase');

        // すでに購入済みなら何もしない
        if ($item->purchase) {
            return redirect()->route('items.index');
        }

        // checkout() で入れた情報
        $itemId = session()->pull('stripe_item_id');
        $pm     = session()->pull('stripe_pm');

        if ((int)$itemId !== (int)$item->id) {
            return redirect()->route('items.show', $item);
        }

        // 配送先：purchase_address のセッションがあれば優先
        $key  = "purchase_address.{$item->id}";
        $addr = session($key, []);

        $shipping_postal_code = $addr['shipping_postal_code'] ?? $request->user()->postal_code;
        $shipping_address     = $addr['shipping_address'] ?? $request->user()->address;
        $shipping_building    = $addr['shipping_building'] ?? $request->user()->building;

        // 住所が無いなら購入画面へ戻す
        if (!$shipping_postal_code || !$shipping_address) {
            return redirect()
                ->route('purchase.show', $item)
                ->withErrors(['shipping' => '送付先住所を入力してください。']);
        }

        Purchase::create([
            'item_id'              => $item->id,
            'buyer_id'             => $request->user()->id,
            'payment_method'       => $pm,
            'shipping_postal_code' => $shipping_postal_code,
            'shipping_address'     => $shipping_address,
            'shipping_building'    => $shipping_building,
            'stripe_session_id'    => $sessionId,
            'status'               => 'paid',
            'purchased_at'         => now(),
        ]);

        // 住所セッションもクリア
        session()->forget($key);

        // 要件：購入後は一覧へ
        return redirect()->route('items.index')->with('status', '購入が完了しました。');
    }

    public function cancel(Request $request, Item $item)
    {
        return redirect()->route('purchase.show', $item)->withErrors([
            'payment' => '決済がキャンセルされました。',
        ]);
    }
}
