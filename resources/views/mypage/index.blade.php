@extends('layouts.app')

@section('title', 'マイページ')

@php
  // ✅ プロフィール画像URL判定（storage / public直置き / 外部URL対応）
  // ※ カラム名は環境により異なるので候補を吸収（確定してるなら1つに絞ってOK）
  $path = (string) ($user->profile_image ?? $user->profile_image_path ?? $user->image_path ?? '');
  $profileImg = '';

  if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
      $profileImg = $path;
  } elseif (\Illuminate\Support\Str::startsWith($path, 'public/')) {
      $profileImg = asset(\Illuminate\Support\Str::after($path, 'public/'));
  } elseif ($path !== '') {
      $profileImg = \Illuminate\Support\Facades\Storage::url($path);
  }
@endphp

@section('content')
  <style>
    .head { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap; margin-bottom:14px; }
    .card { background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:16px; }
    .tabs { display:flex; gap:10px; margin:14px 0; flex-wrap:wrap; }
    .tabbtn { padding:8px 14px; border-radius:999px; border:1px solid #ddd; background:#fff; cursor:pointer; }
    .tabbtn.active { background:#111; color:#fff; border-color:#111; }
    .grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:14px; }
    @media (max-width: 980px) { .grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 720px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    .item { display:block; text-decoration:none; color:#111; background:#fff; border:1px solid #e5e5e5; border-radius:16px; overflow:hidden; }
    .thumb { position:relative; aspect-ratio:1/1; background:#eee; }
    .thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .sold { position:absolute; top:10px; left:10px; background:rgba(0,0,0,.75); color:#fff; padding:6px 10px; border-radius:999px; font-size:12px; }
    .body { padding:12px; }
    .name { font-weight:700; }
    .muted { color:#666; font-size:12px; margin-top:6px; }
    .pager { margin-top:14px; }
  </style>

  <div class="head">
    <div class="card" style="flex:1; min-width:260px;">

      {{-- ✅ プロフィール画像 + 名前/メール --}}
      <div style="display:flex; gap:12px; align-items:center; margin-bottom:10px;">
        <div style="width:72px; height:72px; border-radius:999px; background:#eee; overflow:hidden; flex:0 0 auto;">
          @if($profileImg)
            <img src="{{ $profileImg }}" alt="プロフィール画像" style="width:100%; height:100%; object-fit:cover; display:block;">
          @endif
        </div>

        <div>
          <div style="font-weight:800; font-size:18px;">{{ $user->name }}</div>
          <div style="color:#666; margin-top:6px;">{{ $user->email }}</div>
        </div>
      </div>

      <div style="margin-top:12px;">
        <a href="{{ route('profile.edit') }}" style="text-decoration:none;">プロフィールを編集</a>
      </div>

      <div style="color:#666; margin-top:10px; font-size:13px;">
        〒{{ $user->postal_code ?? '—' }}<br>
        {{ $user->address ?? '—' }}<br>
        {{ $user->building ?? '' }}
      </div>
    </div>
  </div>

  <div class="tabs">
    <button class="tabbtn active" type="button" data-tab="purchased">購入した商品</button>
    <button class="tabbtn" type="button" data-tab="selling">出品した商品</button>
  </div>

  {{-- 購入商品（PG11） --}}
  <section id="tab-purchased">
    @if($purchasedItems->count() === 0)
      <div class="card" style="color:#666;">購入した商品はまだありません。</div>
    @else
      <div class="grid">
        @foreach($purchasedItems as $item)
          @include('mypage.partials.item_card', ['item' => $item])
        @endforeach
      </div>
      <div class="pager">{{ $purchasedItems->links() }}</div>
    @endif
  </section>

  {{-- 出品商品（PG12） --}}
  <section id="tab-selling" style="display:none;">
    @if($sellingItems->count() === 0)
      <div class="card" style="color:#666;">出品した商品はまだありません。</div>
    @else
      <div class="grid">
        @foreach($sellingItems as $item)
          @include('mypage.partials.item_card', ['item' => $item, 'mode' => 'selling'])
        @endforeach
      </div>
      <div class="pager">{{ $sellingItems->links() }}</div>
    @endif
  </section>

  <script>
    const btns = document.querySelectorAll('[data-tab]');
    const purchased = document.getElementById('tab-purchased');
    const selling = document.getElementById('tab-selling');

    btns.forEach(btn => {
      btn.addEventListener('click', () => {
        btns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const tab = btn.dataset.tab;
        purchased.style.display = (tab === 'purchased') ? '' : 'none';
        selling.style.display   = (tab === 'selling') ? '' : 'none';
      });
    });
  </script>
@endsection
