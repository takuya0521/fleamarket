<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        // 購入した商品（PG11）
        $purchasedItems = Item::query()
            ->whereHas('purchase', fn ($q) => $q->where('buyer_id', $userId))
            ->with(['purchase'])
            ->latest()
            ->paginate(12, ['*'], 'purchased_page');

        // 出品した商品（PG12）
        $sellingItems = Item::query()
            ->where('seller_id', $userId)
            ->with(['purchase'])
            ->latest()
            ->paginate(12, ['*'], 'selling_page');

        // 取引中の商品（FN001）
        // buyer/seller どちらの立場でも関与している purchase を対象にする
        $activePurchasesQuery = Purchase::query()
            ->where(function ($q) use ($userId) {
                $q->where('buyer_id', $userId)
                    ->orWhereHas('item', fn ($iq) => $iq->where('seller_id', $userId));
            })
            ->with(['item.seller', 'buyer']);

        // buyer_completed_at がある場合は「取引中（未完了）」に寄せる
        if (Schema::hasColumn('purchases', 'buyer_completed_at')) {
            $activePurchasesQuery->whereNull('buyer_completed_at');
        }

        // last_message_at / unread_count を付与して並び順も最新メッセージ順にする
        if (Schema::hasTable('purchase_messages') && Schema::hasTable('purchase_reads')) {
            $activePurchasesQuery
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
                            'purchase_messages.created_at > COALESCE((select last_read_at from purchase_reads where purchase_reads.purchase_id = purchases.id and purchase_reads.user_id = ? limit 1), "1970-01-01 00:00:00")',
                            [$userId]
                        );
                }, 'unread_count')
                ->orderByRaw('COALESCE(last_message_at, purchased_at, created_at) DESC');
        } else {
            // テーブルがまだ無いなどの場合でも落ちないように最低限
            $activePurchasesQuery->latest();
        }

        $activePurchases = $activePurchasesQuery->get();

        // 未読メッセージ合計（FN001）
        // 0でも表示したいので、必ず数値を渡す
        $transactionUnreadCount = 0;
        if ($activePurchases->count() > 0) {
            $transactionUnreadCount = (int) $activePurchases->sum(function ($p) {
                return (int)($p->unread_count ?? 0);
            });
        }

        // 取引評価平均（要件）
        // - まだ評価がないユーザーは表示しない（nullのまま）
        // - 小数は四捨五入
        $userRatingAvg = null;      // float|null
        $userRatingRounded = null;  // int|null

        if (Schema::hasTable('transaction_ratings')) {
            $avg = DB::table('transaction_ratings')
                ->where('ratee_id', $userId)
                ->avg('score'); // 0件なら null

            if (!is_null($avg)) {
                $userRatingAvg = (float) $avg;
                $userRatingRounded = (int) round($userRatingAvg, 0, PHP_ROUND_HALF_UP);

                // 念のため 1〜5 に収める
                $userRatingRounded = max(1, min(5, $userRatingRounded));
            }
        }

        return view('mypage.index', compact(
            'user',
            'purchasedItems',
            'sellingItems',
            'activePurchases',
            'transactionUnreadCount',
            'userRatingAvg',
            'userRatingRounded'
        ));
    }
}