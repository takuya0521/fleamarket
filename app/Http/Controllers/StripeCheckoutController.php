<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class StripeCheckoutController extends Controller
{
    public function createItem(Request $request, Item $item)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => (int) $item->price,
                ],
                'quantity' => 1,
            ]],
            'success_url' => url('/checkout/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => url('/checkout/cancel'),

            // webhookで誰が何を買ったか復元する
            'metadata' => [
                'user_id' => (string) $request->user()->id,
                'item_id' => (string) $item->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        return "Payment processing. You can close this page.";
    }

    public function cancel()
    {
        return "Canceled.";
    }
}
