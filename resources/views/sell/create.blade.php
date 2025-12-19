@extends('layouts.app')

@section('title', '出品')

@section('content')
  <style>
    .panel { max-width:820px; margin:0 auto; background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px; }
    .field { margin-top:12px; }
    label { display:block; font-size:13px; color:#555; margin-bottom:6px; }
    input[type="text"], input[type="number"], textarea, select {
      width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:12px;
    }
    textarea { min-height:120px; resize:vertical; }
    .grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    @media (max-width: 820px){ .grid{ grid-template-columns:1fr; } }

    .cats { display:flex; flex-wrap:wrap; gap:10px; }
    .cat { display:flex; gap:8px; align-items:center; padding:8px 10px; border:1px solid #ddd; border-radius:999px; background:#fff; }
    .btn { display:inline-flex; align-items:center; justify-content:center; height:40px; padding:0 14px; border-radius:12px; border:1px solid #111; background:#111; color:#fff; cursor:pointer; }
    .btn.ghost { background:#fff; color:#111; border-color:#ddd; text-decoration:none; }
    .err { background:#fff5f5; border:1px solid #ffd1d1; padding:12px; border-radius:12px; margin-bottom:12px; }

    /* 項目エラー表示 */
    .invalid { border-color:#ff6b6b !important; background:#fffafa; }
    .msg { margin:6px 0 0; color:#d63031; font-size:12px; }
  </style>

  <div style="margin-bottom:12px;">
    <a href="{{ route('items.index') }}">← 一覧へ戻る</a>
  </div>

  <div class="panel">
    <h1 style="margin:0 0 14px;">商品を出品</h1>

    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" novalidate>
      @csrf

      <div class="field">
        <label>商品画像（必須）</label>
        <input
          type="file"
          name="image"
          accept=".jpg,.jpeg,.png,image/jpeg,image/png"
          class="@error('image') invalid @enderror"
        >
        @error('image')
          <p class="msg">{{ $message }}</p>
        @enderror
      </div>

      <div class="grid">
        <div class="field">
          <label>商品名（必須）</label>
          <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            maxlength="255"
            class="@error('name') invalid @enderror"
          >
          @error('name')
            <p class="msg">{{ $message }}</p>
          @enderror
        </div>

        <div class="field">
          <label>ブランド（任意）</label>
          <input
            type="text"
            name="brand"
            value="{{ old('brand') }}"
            maxlength="255"
            class="@error('brand') invalid @enderror"
          >
          @error('brand')
            <p class="msg">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="field">
        <label>カテゴリ（1つ以上）</label>

        <div class="cats">
          @foreach($categories as $cat)
            <label class="cat">
              <input
                type="checkbox"
                name="categories[]"
                value="{{ $cat->id }}"
                @checked(is_array(old('categories')) && in_array($cat->id, old('categories')))
              >
              {{ $cat->name }}
            </label>
          @endforeach
        </div>

        @error('categories')
          <p class="msg">{{ $message }}</p>
        @enderror
        @error('categories.*')
          <p class="msg">{{ $message }}</p>
        @enderror
      </div>

      <div class="grid">
        <div class="field">
          <label>商品の状態（必須）</label>
          <select
            name="condition"
            class="@error('condition') invalid @enderror"
          >
            <option value="">選択してください</option>
            @foreach(['新品、未使用','目立った傷や汚れなし','やや傷や汚れあり','傷や汚れあり','全体的に状態が悪い'] as $c)
              <option value="{{ $c }}" @selected(old('condition')===$c)>{{ $c }}</option>
            @endforeach
          </select>
          @error('condition')
            <p class="msg">{{ $message }}</p>
          @enderror
        </div>

        <div class="field">
          <label>価格（必須）</label>
          <input
            type="number"
            name="price"
            value="{{ old('price') }}"
            min="0"
            step="1"
            inputmode="numeric"
            class="@error('price') invalid @enderror"
          >
          @error('price')
            <p class="msg">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="field">
        <label>商品説明（必須）</label>
        <textarea
          name="description"
          maxlength="255"
          class="@error('description') invalid @enderror"
        >{{ old('description') }}</textarea>
        @error('description')
          <p class="msg">{{ $message }}</p>
        @enderror
      </div>

      <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
        <button class="btn" type="submit">出品する</button>
        <a class="btn ghost" href="{{ route('items.index') }}">キャンセル</a>
      </div>
    </form>
  </div>
@endsection
