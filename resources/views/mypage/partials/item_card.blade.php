@php
  $img = '';

  if (!empty($item->image_url)) {
    $img = $item->image_url;
  } else {
    $p = (string)($item->image_path ?? '');
    if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) {
      $img = $p;
    } elseif (\Illuminate\Support\Str::startsWith($p, 'public/')) {
      $img = asset('storage/' . \Illuminate\Support\Str::after($p, 'public/'));
    } elseif ($p !== '') {
      $img = \Illuminate\Support\Facades\Storage::url($p);
    }
  }

  // Sold判定：purchaseがあればSold（with('purchase') 前提）
  $isSold = !empty($item->purchase);
@endphp

<a class="mp-item-card" href="{{ url('/item/' . $item->id) }}">
  <div class="mp-item-thumb">
    @if($img)
      <img src="{{ $img }}" alt="{{ $item->name }}">
    @endif

    @if($isSold)
      <span class="mp-item-sold">Sold</span>
    @endif
  </div>

  <div class="mp-item-body">
    <div class="mp-item-name">{{ $item->name }}</div>
    <div class="mp-item-price">¥{{ number_format((int)$item->price) }}</div>
  </div>
</a>