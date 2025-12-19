<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseAddressController;

use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;

use App\Http\Controllers\StripeController;

/**
 * 公開：商品一覧 / 商品詳細
 */
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/home', fn () => redirect('/'))->name('home');

/**
 * 認証だけ必要（未認証ユーザーはログインへ）
 */
Route::post('/item/{item}/like', [LikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('items.like');

Route::post('/item/{item}/comment', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('items.comment');

/**
 * メール認証誘導ページ（あなたのカスタム）
 */
Route::middleware('auth')->get('/verify/notice', function () {
    return view('auth.verify-notice');
})->name('verify.notice.custom');

/**
 * 認証 + メール認証が必要なページ
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 配送先変更（←これが無いと purchase.address.edit not defined で落ちる）
    Route::get('/purchase/address/{item}', [PurchaseAddressController::class, 'edit'])
        ->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseAddressController::class, 'update'])
        ->name('purchase.address.update');

    // 購入完了
    Route::get('/purchase/complete/{item}', [PurchaseController::class, 'complete'])
        ->name('purchase.complete');

    // 出品
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    Route::get('/sell/{item}/edit', [SellController::class, 'edit'])->name('sell.edit');
    Route::post('/sell/{item}/update', [SellController::class, 'update'])->name('sell.update');
    Route::post('/sell/{item}/delete', [SellController::class, 'destroy'])->name('sell.destroy');

    // Stripe（決済）
    Route::post('/stripe/checkout/{item}', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success/{item}',  [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel/{item}',   [StripeController::class, 'cancel'])->name('stripe.cancel');
});
