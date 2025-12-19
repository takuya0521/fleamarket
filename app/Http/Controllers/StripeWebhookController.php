<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // .env → config/services.php

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (UnexpectedValueException $e) {
            Log::warning('stripe webhook invalid payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('stripe webhook invalid signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        Log::info('stripe webhook received', [
            'id'   => $event->id ?? null,
            'type' => $event->type ?? null,
        ]);

        switch ($event->type) {
            case 'checkout.session.completed': {
                /** @var \Stripe\Checkout\Session $session */
                $session = $event->data->object;

                // 例：metadata で user/course/item を取る（あなたの設計に合わせて）
                $userId = (int)($session->metadata->user_id ?? 0);
                $courseId = (int)($session->metadata->course_id ?? 0);

                Log::info('checkout completed', [
                    'session_id' => $session->id ?? null,
                    'user_id'    => $userId,
                    'course_id'  => $courseId,
                ]);

                // TODO: ここでDB更新（購入確定・権限付与・二重処理防止）
                break;
            }

            default:
                // 何もしない
                break;
        }

        return response('OK', 200);
    }
}
