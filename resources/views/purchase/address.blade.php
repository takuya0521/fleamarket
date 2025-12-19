@extends('layouts.app')

@section('title', '送付先住所の変更')

@section('content')
  <style>
    .card { background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:16px; max-width:720px; }
    .row { display:flex; flex-direction:column; gap:8px; margin-top:12px; }
    label { font-size:13px; color:#666; }
    input{
      width:100%;
      padding:10px 12px;
      border:1px solid #ddd;
      border-radius:12px;
      background:#fff;
      outline:none;
    }
    input:focus{ border-color:#bbb; }
    .btnrow{ display:flex; gap:10px; margin-top:14px; flex-wrap:wrap; }
    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      height:40px; padding:0 14px; border-radius:12px;
      border:1px solid #ddd; background:#fff; color:#111;
      text-decoration:none; cursor:pointer;
    }
    .btn.primary{ background:#111; border-color:#111; color:#fff; }
    .err{ background:#fff5f5; border:1px solid #ffd1d1; padding:12px; border-radius:12px; margin-top:12px; }
  </style>

  <div class="card">
    <div style="font-weight:800; font-size:18px;">送付先住所の変更</div>
    <div style="color:#666; margin-top:6px;">「購入画面」に反映されます。</div>

    @if ($errors->any())
      <div class="err">
        <ul style="margin:0; padding-left:18px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" style="margin-top:12px;">
      @csrf

      <div class="row">
        <label>郵便番号</label>
        <input name="shipping_postal_code" value="{{ old('shipping_postal_code', $shipping_postal_code ?? '') }}" placeholder="例）123-4567">
      </div>

      <div class="row">
        <label>住所</label>
        <input name="shipping_address" value="{{ old('shipping_address', $shipping_address ?? '') }}" placeholder="例）東京都渋谷区…">
      </div>

      <div class="row">
        <label>建物名</label>
        <input name="shipping_building" value="{{ old('shipping_building', $shipping_building ?? '') }}" placeholder="任意">
      </div>

      <div class="btnrow">
        <button class="btn primary" type="submit">更新する</button>
        <a class="btn" href="{{ route('purchase.show', $item) }}">戻る</a>
      </div>
    </form>
  </div>
@endsection
