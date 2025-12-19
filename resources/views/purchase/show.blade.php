@extends('layouts.app')

@section('title', '購入')

@php
  $path = (string) ($item->image_path ?? '');

  if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
      // S3などの外部URL
      $img = $path;
  } elseif (\Illuminate\Support\Str::startsWith($path, 'public/')) {
      // public/xxx の場合（public直置き想定）
      $img = asset(\Illuminate\Support\Str::after($path, 'public/'));
  } elseif ($path !== '') {
      // storage/app/public 配下など（storage:link 前提）
      $img = \Illuminate\Support\Facades\Storage::url($path);
  } else {
      $img = '';
  }
@endphp

@section('content')
  <style>
    .invalid { border-color:#ff6b6b !important; background:#fffafa; }
    .msg { margin:6px 0 0; color:#d63031; font-size:12px; }

    .grid { display:grid; grid-template-columns: 1fr 380px; gap:18px; align-items:start; }
    @media (max-width: 980px){ .grid{ grid-template-columns:1fr; } }

    .panel { background:#fff; border:1px solid #e5e5e5; border-radius:16px; overflow:hidden; }
    .box { padding:16px; }
    .row { display:flex; justify-content:space-between; gap:12px; align-items:center; }

    .thumb { aspect-ratio:1/1; background:#eee; }
    .thumb img { width:100%; height:100%; object-fit:cover; display:block; }

    .muted { color:#666; font-size:13px; }

    .btn { display:inline-flex; align-items:center; justify-content:center; height:40px; padding:0 14px; border-radius:12px; border:1px solid #ddd; background:#fff; cursor:pointer; text-decoration:none; color:#111; }
    .btn.primary { background:#111; border-color:#111; color:#fff; }

    select { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:12px; background:#fff; }
  </style>

  <div style="margin-bottom:12px;">
    <a href="{{ route('items.show', $item) }}">← 商品詳細へ戻る</a>
  </div>

  @if (session('status'))
    <div style="background:#f0fff4; border:1px solid #b7f5c5; padding:12px; border-radius:12px; margin-bottom:12px;">
      {{ session('status') }}
    </div>
  @endif

  <div class="grid">
    <div class="panel">
      <div class="thumb">
        @if($img)
          <img src="{{ $img }}" alt="{{ $item->name }}">
        @endif
      </div>
      <div class="box">
        <h1 style="margin:0 0 6px;">{{ $item->name }}</h1>
        <div class="muted">¥{{ number_format($item->price) }}</div>
      </div>
    </div>

    <div class="panel">
      <div class="box">
        <div class="row">
          <div>
            <div style="font-weight:800;">送付先</div>
            <div class="muted" style="margin-top:6px;">
              〒{{ $shipping_postal_code ?? '—' }}<br>
              {{ $shipping_address ?? '—' }}<br>
              {{ $shipping_building ?? '' }}
            </div>
          </div>
          <a class="btn" href="{{ route('purchase.address.edit', $item) }}">変更</a>
        </div>

        <hr style="margin:14px 0; border:none; border-top:1px solid #eee;">

        <form method="POST" action="{{ route('purchase.store', $item) }}" novalidate>
          @csrf

          <div style="font-weight:800; margin-bottom:8px;">支払い方法</div>

          <select name="payment_method" class="@error('payment_method') invalid @enderror">
            <option value="">選択してください</option>
            <option value="convenience_store" @selected(old('payment_method')==='convenience_store')>コンビニ払い</option>
            <option value="card" @selected(old('payment_method')==='card')>クレジットカード</option>
          </select>

          @error('payment_method')
            <p class="msg">{{ $message }}</p>
          @enderror

          <button class="btn primary" type="submit" style="width:100%; margin-top:14px;">
            購入する
          </button>
        </form>

      </div>
    </div>
  </div>
@endsection
