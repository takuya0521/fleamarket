@extends('layouts.app')

@section('title', '購入完了')

@section('content')
  <style>
    .panel { max-width:720px; margin:0 auto; background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px; }
    .title { margin:0 0 10px; font-size:20px; font-weight:800; }
    .muted { color:#666; font-size:13px; }
    .row { display:flex; gap:14px; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; }
    .box { border:1px solid #eee; border-radius:14px; padding:14px; background:#fafafa; }
    .btn { display:inline-flex; align-items:center; justify-content:center; height:40px; padding:0 14px; border-radius:12px; border:1px solid #111; background:#111; color:#fff; cursor:pointer; text-decoration:none; }
    .btn.ghost { background:#fff; color:#111; border-color:#ddd; }
  </style>

  <div class="panel">
    <h1 class="title">購入が完了しました</h1>
    <div class="muted">ご購入ありがとうございます。</div>

    <hr style="margin:14px 0; border:none; border-top:1px solid #eee;">

    <div class="row">
      <div style="flex:1; min-width:260px;">
        <div style="font-weight:800; margin-bottom:8px;">購入商品</div>
        <div class="box">
          <div style="font-weight:700;">{{ $item->name }}</div>
          <div class="muted" style="margin-top:6px;">¥{{ number_format($item->price) }}</div>
        </div>
      </div>

      <div style="flex:1; min-width:260px;">
        <div style="font-weight:800; margin-bottom:8px;">お届け先</div>
        <div class="box">
          <div class="muted">
            〒{{ $purchase->shipping_postal_code }}<br>
            {{ $purchase->shipping_address }}<br>
            {{ $purchase->shipping_building ?? '' }}
          </div>
        </div>
      </div>
    </div>

    <div style="margin-top:14px;">
      <div style="font-weight:800; margin-bottom:8px;">支払い方法</div>
      <div class="box">
        <div class="muted">
          @if($purchase->payment_method === 'card')
            クレジットカード
          @elseif($purchase->payment_method === 'convenience_store')
            コンビニ払い
          @else
            {{ $purchase->payment_method }}
          @endif
        </div>
      </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
      <a class="btn" href="{{ route('items.index') }}">トップへ戻る</a>
      <a class="btn ghost" href="{{ route('items.show', $item) }}">商品詳細へ戻る</a>
    </div>
  </div>
@endsection
