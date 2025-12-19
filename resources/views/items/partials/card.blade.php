@php
  $sold = !is_null($item->purchase ?? null);

  $path = (string) ($item->image_path ?? '');
  if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
      $img = $path; // ✅ 外部URLはそのまま
  } elseif (\Illuminate\Support\Str::startsWith($path, 'public/')) {
      $img = asset(\Illuminate\Support\Str::after($path, 'public/'));
  } elseif ($path !== '') {
      $img = \Illuminate\Support\Facades\Storage::url($path);
  } else {
      $img = '';
  }

  $liked = auth()->check()
    ? (isset($likedIds) ? in_array($item->id, $likedIds, true) : false)
    : false;
@endphp

<div class="card" style="position:relative;">
  <a href="{{ route('items.show', $item) }}" style="display:block; text-decoration:none; color:inherit;">
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
      <div class="meta">
        <span>❤ {{ $item->likes_count ?? 0 }}</span>
        <span>💬 {{ $item->comments_count ?? 0 }}</span>
      </div>
    </div>
  </a>

  @auth
    <form method="POST" action="{{ route('items.like', $item) }}"
          style="position:absolute; right:10px; top:10px;">
      @csrf
      <button type="submit"
              style="border:none; cursor:pointer; padding:8px 10px; border-radius:999px; background:rgba(255,255,255,.92); border:1px solid #e5e5e5;">
        {{ $liked ? '♥' : '♡' }}
      </button>
    </form>
  @endauth
</div>
