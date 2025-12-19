@php
  $mode = $mode ?? null;

  $path = (string) ($item->image_path ?? '');
  $img = '';

  if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
      $img = $path; // ✅ そのまま使う
  } elseif (\Illuminate\Support\Str::startsWith($path, 'public/')) {
      $img = asset(\Illuminate\Support\Str::after($path, 'public/'));
  } elseif ($path !== '') {
      $img = \Illuminate\Support\Facades\Storage::url($path);
  }

  $sold = !is_null($item->purchase ?? null);
@endphp

@if($mode === 'selling')
  {{-- 出品タブ：aタグ入れ子を避けるため、カード全体リンクにしない --}}
  <div class="item">
    <div class="thumb">
      <a href="{{ route('items.show', $item) }}" style="display:block; width:100%; height:100%;">
        @if($img)
          <img src="{{ $img }}" alt="{{ $item->name }}">
        @endif
      </a>
      @if($sold)
        <div class="sold">Sold</div>
      @endif
    </div>

    <div class="body">
      <a href="{{ route('items.show', $item) }}" style="text-decoration:none; color:#111;">
        <div class="name">{{ $item->name }}</div>
        <div class="muted">¥{{ number_format($item->price) }}</div>
      </a>

      @if(auth()->check() && $item->seller_id === auth()->id() && !$sold)
        <div style="margin-top:8px;">
          <a href="{{ route('sell.edit', $item) }}"
             style="font-size:12px; text-decoration:none; color:#111; font-weight:700;">
            編集する →
          </a>
        </div>
      @endif
    </div>
  </div>

@else
  {{-- 購入タブ：カード全体を詳細リンクのままでOK --}}
  <a class="item" href="{{ route('items.show', $item) }}">
    <div class="thumb">
      @if($img)
        <img src="{{ $img }}" alt="{{ $item->name }}">
      @endif
      @if($sold)
        <div class="sold">Sold</div>
      @endif
    </div>

    <div class="body">
      <div class="name">{{ $item->name }}</div>
      <div class="muted">¥{{ number_format($item->price) }}</div>
    </div>
  </a>
@endif
