<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionMessageRequest;
use App\Mail\TransactionCompletedMail;
use App\Models\Purchase;
use App\Models\PurchaseMessage;
use App\Models\PurchaseRead;
use App\Models\TransactionRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $purchases = $this->basePurchaseQuery($userId)
            ->select('purchases.*')
            ->selectSub(function ($q) {
                $q->from('purchase_messages')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('purchase_messages.purchase_id', 'purchases.id')
                    ->whereNull('purchase_messages.deleted_at');
            }, 'last_message_at')
            ->selectSub(function ($q) use ($userId) {
                $q->from('purchase_messages')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('purchase_messages.purchase_id', 'purchases.id')
                    ->whereNull('purchase_messages.deleted_at')
                    ->where('purchase_messages.sender_id', '!=', $userId)
                    ->whereRaw(
                        'purchase_messages.created_at > COALESCE((select last_read_at from purchase_reads where purchase_reads.purchase_id = purchases.id and purchase_reads.user_id = ? limit 1), "1970-01-01")',
                        [$userId]
                    );
            }, 'unread_count')
            ->orderByRaw('COALESCE(last_message_at, purchased_at, created_at) DESC')
            ->paginate(20);

        return view('transactions.index', compact('purchases'));
    }

    public function show(Request $request, Purchase $purchase)
    {
        $user = $request->user();
        $purchase->load(['item.seller', 'buyer']);

        // 参加者以外NG
        $isBuyer = $purchase->buyer_id === $user->id;
        $isSeller = $purchase->item->seller_id === $user->id;
        if (!$isBuyer && !$isSeller) {
            return redirect()->route('mypage.index');
        }

        // メッセージ
        $messages = PurchaseMessage::query()
            ->where('purchase_id', $purchase->id)
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        // 既読更新（開いた時点で読む扱い）
        PurchaseRead::updateOrCreate(
            ['purchase_id' => $purchase->id, 'user_id' => $user->id],
            ['last_read_at' => now()]
        );

        // サイドバー用（別取引へ遷移）
        $sidebarPurchases = $this->basePurchaseQuery($user->id)
            ->select('purchases.*')
            ->selectSub(function ($q) {
                $q->from('purchase_messages')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('purchase_messages.purchase_id', 'purchases.id')
                    ->whereNull('purchase_messages.deleted_at');
            }, 'last_message_at')
            ->selectSub(function ($q) use ($user) {
                $q->from('purchase_messages')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('purchase_messages.purchase_id', 'purchases.id')
                    ->whereNull('purchase_messages.deleted_at')
                    ->where('purchase_messages.sender_id', '!=', $user->id)
                    ->whereRaw(
                        'purchase_messages.created_at > COALESCE((select last_read_at from purchase_reads where purchase_reads.purchase_id = purchases.id and purchase_reads.user_id = ? limit 1), "1970-01-01")',
                        [$user->id]
                    );
            }, 'unread_count')
            ->orderByRaw('COALESCE(last_message_at, purchased_at, created_at) DESC')
            ->get();

        // 評価済み判定
        $buyerRated = TransactionRating::where('purchase_id', $purchase->id)
            ->where('rater_id', $purchase->buyer_id)
            ->exists();

        $sellerRated = TransactionRating::where('purchase_id', $purchase->id)
            ->where('rater_id', $purchase->item->seller_id)
            ->exists();

        return view('transactions.show', compact(
            'purchase',
            'messages',
            'sidebarPurchases',
            'isBuyer',
            'isSeller',
            'buyerRated',
            'sellerRated'
        ));
    }

    /**
     * 取引メッセージ送信（FormRequestバリデーション）
     * - 本文：必須、最大400
     * - 画像：.jpeg / .png のみ
     */
    public function storeMessage(StoreTransactionMessageRequest $request, Purchase $purchase)
    {
        $user = $request->user();
        $purchase->load('item');

        $isBuyer = $purchase->buyer_id === $user->id;
        $isSeller = $purchase->item->seller_id === $user->id;
        if (!$isBuyer && !$isSeller) {
            return redirect()->route('mypage.index');
        }

        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            // public disk に保存 → /storage/transaction_messages/... で表示できる
            $imagePath = $request->file('image')->store('transaction_messages', 'public');
        }

        PurchaseMessage::create([
            'purchase_id' => $purchase->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
            'image_path' => $imagePath,
        ]);

        // ✅ 送信成功時だけ、下書き削除用のフラグをセッションへ
        return redirect()
            ->to(route('transactions.show', $purchase) . '#message-form')
            ->with('message_sent', true);
    }

    // 購入者：取引を完了（評価＋メール送信）
    public function complete(Request $request, Purchase $purchase)
    {
        $user = $request->user();
        $purchase->load(['item.seller', 'buyer']);

        if ($purchase->buyer_id !== $user->id) {
            return redirect()->route('transactions.show', $purchase);
        }

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        DB::transaction(function () use ($purchase, $validated) {
            if (is_null($purchase->buyer_completed_at)) {
                $purchase->buyer_completed_at = now();
                $purchase->save();
            }

            // buyer -> seller
            TransactionRating::firstOrCreate(
                ['purchase_id' => $purchase->id, 'rater_id' => $purchase->buyer_id],
                ['ratee_id' => $purchase->item->seller_id, 'score' => $validated['score']]
            );
        });

        // メール送信（FN016）
        Mail::to($purchase->item->seller->email)->send(new TransactionCompletedMail($purchase));

        return redirect()->route('items.index');
    }

    // 出品者：購入者が完了後に評価
    public function rateBySeller(Request $request, Purchase $purchase)
    {
        $user = $request->user();
        $purchase->load(['item.seller', 'buyer']);

        if ($purchase->item->seller_id !== $user->id) {
            return redirect()->route('transactions.show', $purchase);
        }
        if (is_null($purchase->buyer_completed_at)) {
            return redirect()->route('transactions.show', $purchase);
        }

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        TransactionRating::firstOrCreate(
            ['purchase_id' => $purchase->id, 'rater_id' => $user->id],
            ['ratee_id' => $purchase->buyer_id, 'score' => $validated['score']]
        );

        return redirect()->route('items.index');
    }

    private function basePurchaseQuery(int $userId)
    {
        return Purchase::query()
            ->where('buyer_id', $userId)
            ->orWhereHas('item', fn ($q) => $q->where('seller_id', $userId))
            ->with(['item.seller', 'buyer']);
    }
}