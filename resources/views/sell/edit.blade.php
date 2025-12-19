@extends('layouts.app')

@section('title', '商品編集')

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
    .btn.danger { background:#b91c1c; border-color:#b91c1c; }
    .err { background:#fff5f5; border:1px solid #ffd1d1; padding:12px; border-radius:12px; margin-bottom:12px; }
  </style>

  <div style="margin-bottom:12px;">
    <a href="{{ route('items.show', $item) }}">← 商品詳細へ戻る</a>
  </div>

  @if ($errors->any())
    <div class="err">
      <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="panel">
    <h1 style="margin:0 0 14px;">商品を編集</h1>

    <form method="POST" action="{{ route('sell.update', $item) }}" enctype="multipart/form-data">
      @csrf

      <div class="field">
        <label>商品画像（変更する場合のみ）</label>
        <input type="file" name="image" accept="image/*">
      </div>

      <div class="grid">
        <div class="field">
          <label>商品名</label>
          <input type="text" name="name" value="{{ old('name', $item->name) }}" required>
        </div>

        <div class="field">
          <label>ブランド（任意）</label>
          <input type="text" name="brand" value="{{ old('brand', $item->brand) }}">
        </div>
      </div>

      <div class="field">
        <label>カテゴリ（1つ以上）</label>
        <div class="cats">
          @foreach($categories as $cat)
            <label class="cat">
              <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                @checked(in_array($cat->id, old('categories', $selected ?? [])))>
              {{ $cat->name }}
            </label>
          @endforeach
        </div>
      </div>

      <div class="grid">
        <div class="field">
          <label>商品の状態</label>
          <select name="condition" required>
            @foreach(['新品、未使用','目立った傷や汚れなし','やや傷や汚れあり','傷や汚れあり','全体的に状態が悪い'] as $c)
              <option value="{{ $c }}" @selected(old('condition', $item->condition)===$c)>{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label>価格</label>
          <input type="number" name="price" value="{{ old('price', $item->price) }}" min="1" required>
        </div>
      </div>

      <div class="field">
        <label>商品説明</label>
        <textarea name="description" required>{{ old('description', $item->description) }}</textarea>
      </div>

      <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
        <button class="btn" type="submit">更新する</button>
        <a class="btn ghost" href="{{ route('items.show', $item) }}">キャンセル</a>
      </div>
    </form>

    <hr style="margin:18px 0; border:none; border-top:1px solid #eee;">

    <form method="POST" action="{{ route('sell.destroy', $item) }}"
          onsubmit="return confirm('本当に削除しますか？');">
      @csrf
      <button class="btn danger" type="submit">削除する</button>
    </form>
  </div>
@endsection
