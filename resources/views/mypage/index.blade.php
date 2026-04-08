@extends('layouts.app')

@section('title', 'マイページ')

@php
  // プロフィール画像URL判定（storage / public直置き / 外部URL対応）
  $path = (string) ($user->profile_image ?? $user->profile_image_path ?? $user->image_path ?? '');
  $profileImg = '';

  if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
      $profileImg = $path;
  } elseif (\Illuminate\Support\Str::startsWith($path, 'public/')) {
      $profileImg = asset(\Illuminate\Support\Str::after($path, 'public/'));
  } elseif ($path !== '') {
      $profileImg = \Illuminate\Support\Facades\Storage::url($path);
  }

  $unreadTotal = (int)($transactionUnreadCount ?? 0);

  // ★表示用（評価が無いときは null のまま）
  $userRatingRounded = $userRatingRounded ?? null;
@endphp

@section('content')
  <style>
    /* ざっくりFigmaイメージ寄せ */
    .mp-card { background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px; }
    .mp-profile {
      display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
    }
    .mp-profile-left { display:flex; align-items:center; gap:18px; }
    .mp-avatar { width:84px; height:84px; border-radius:999px; background:#e5e5e5; overflow:hidden; flex:0 0 auto; }
    .mp-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .mp-name { font-weight:800; font-size:22px; margin:0; }

    /* ★（評価平均） */
    .mp-stars { margin-top:10px; display:flex; gap:6px; align-items:center; }
    .mp-star { font-size:18px; line-height:1; }
    .mp-star.on { color:#facc15; }
    .mp-star.off { color:#d1d5db; }

    .mp-edit {
      display:inline-flex; align-items:center; justify-content:center;
      height:40px; padding:0 18px; border-radius:10px;
      border:2px solid #ef4444; color:#ef4444; text-decoration:none; font-weight:700;
      background:#fff;
    }

    /* タブ */
    .mp-tabs {
      display:flex; justify-content:center; gap:44px;
      margin-top:18px;
      border-bottom:1px solid #ddd;
    }
    .mp-tab {
      position:relative;
      display:inline-flex;
      align-items:center;
      gap:8px;
      background:transparent;
      border:none;
      padding:14px 6px;
      font-weight:700;
      cursor:pointer;
      color:#333;
      font-size:14px;
    }
    .mp-tab.active {
      color:#ef4444;
      border-bottom:2px solid #ef4444;
      margin-bottom:-1px;
    }
    .mp-badge {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:18px;
      height:18px;
      padding:0 5px;
      border-radius:999px;
      background:#ef4444;
      color:#fff;
      font-size:12px;
      font-weight:800;
      line-height:1;
    }

    .mp-tabpanel { margin-top:18px; }
    .mp-empty { color:#666; background:#fff; border:1px solid #e5e5e5; border-radius:14px; padding:14px; }
    .pager { margin-top:14px; }

    /* ✅ 購入/出品/取引中を同じカードサイズに揃える：共通グリッド */
    .mp-items-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:14px; }
    @media (max-width: 980px) { .mp-items-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 720px) { .mp-items-grid { grid-template-columns: repeat(2, 1fr); } }

    .mp-item-card { display:block; text-decoration:none; color:#111; border:1px solid #e5e5e5; border-radius:16px; overflow:hidden; background:#fff; }
    .mp-item-thumb { position:relative; aspect-ratio: 1 / 1; background:#eee; }
    .mp-item-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .mp-item-sold { position:absolute; top:10px; left:10px; background:rgba(0,0,0,.75); color:#fff; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .mp-item-body { padding:12px; }
    .mp-item-name { font-weight:700; }
    .mp-item-price { color:#111; margin-top:6px; font-size:13px; }

    /* ✅ 取引中カードの未読（各カード左上） */
    .mp-item-unread {
      position:absolute;
      top:10px;
      left:10px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:18px;
      height:18px;
      padding:0 5px;
      border-radius:999px;
      background:#ef4444;
      color:#fff;
      font-size:12px;
      font-weight:800;
      line-height:1;
    }
    .mp-item-meta { color:#111; margin-top:6px; font-size:13px; }
  </style>

  <div class="mp-card">
    <div class="mp-profile">
      <div class="mp-profile-left">
        <div class="mp-avatar">
          @if($profileImg)
            <img src="{{ $profileImg }}" alt="プロフィール画像">
          @endif
        </div>

        <div>
          <h1 class="mp-name">{{ $user->name }}</h1>

          {{-- ✅ 評価平均：評価がある時だけ表示（四捨五入後の★） --}}
          @if(!is_null($userRatingRounded))
            <div class="mp-stars" aria-label="評価">
              @for($i=1; $i<=5; $i++)
                <span class="mp-star {{ $i <= $userRatingRounded ? 'on' : 'off' }}">★</span>
              @endfor
            </div>
          @endif
        </div>
      </div>

      <a class="mp-edit" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>

    <div class="mp-tabs">
      <button type="button" class="mp-tab active" data-tab="selling">出品した商品</button>
      <button type="button" class="mp-tab" data-tab="purchased">購入した商品</button>
      <button type="button" class="mp-tab" data-tab="trading">
        取引中の商品
        @if($unreadTotal > 0)
          <span class="mp-badge">{{ $unreadTotal }}</span>
        @endif
      </button>
    </div>

    {{-- 出品商品（PG12） --}}
    <section id="tab-selling" class="mp-tabpanel">
      @if($sellingItems->count() === 0)
        <div class="mp-empty">出品した商品はまだありません。</div>
      @else
        <div class="mp-items-grid">
          @foreach($sellingItems as $item)
            @include('mypage.partials.item_card', ['item' => $item, 'mode' => 'selling'])
          @endforeach
        </div>
        <div class="pager">{{ $sellingItems->links() }}</div>
      @endif
    </section>

    {{-- 購入商品（PG11） --}}
    <section id="tab-purchased" class="mp-tabpanel" style="display:none;">
      @if($purchasedItems->count() === 0)
        <div class="mp-empty">購入した商品はまだありません。</div>
      @else
        <div class="mp-items-grid">
          @foreach($purchasedItems as $item)
            @include('mypage.partials.item_card', ['item' => $item])
          @endforeach
        </div>
        <div class="pager">{{ $purchasedItems->links() }}</div>
      @endif
    </section>

    {{-- 取引中の商品（FN001）：購入した商品と同じカードサイズ（同じグリッド） --}}
    <section id="tab-trading" class="mp-tabpanel" style="display:none;">
      @if(($activePurchases ?? collect())->count() === 0)
        <div class="mp-empty">現在、取引中の商品はありません。</div>
      @else
        <div class="mp-items-grid">
          @foreach($activePurchases as $purchase)
            @php
              $item = $purchase->item;
              $isBuyer = (int)auth()->id() === (int)$purchase->buyer_id;
              $partner = $isBuyer ? ($item?->seller) : ($purchase->buyer);
              $unread = (int)($purchase->unread_count ?? 0);

              // 画像URL（Itemに image_url アクセサがあるなら優先）
              $img = '';
              if ($item) {
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
              }
            @endphp

            <a class="mp-item-card" href="{{ route('transactions.show', $purchase) }}">
              <div class="mp-item-thumb">
                @if($img)
                  <img src="{{ $img }}" alt="{{ $item?->name ?? '商品' }}">
                @endif

                {{-- 各カード左上の未読バッジ --}}
                @if($unread > 0)
                  <span class="mp-item-unread">{{ $unread }}</span>
                @endif
              </div>

              <div class="mp-item-body">
                <div class="mp-item-name">{{ $item?->name ?? '商品情報なし' }}</div>
                <div class="mp-item-meta">相手：{{ $partner?->name ?? ($partner?->email ?? '—') }}</div>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </section>
  </div>

  <script>
    (function () {
      const tabs = document.querySelectorAll('.mp-tab');
      const selling = document.getElementById('tab-selling');
      const purchased = document.getElementById('tab-purchased');
      const trading = document.getElementById('tab-trading');

      function show(tab) {
        selling.style.display = (tab === 'selling') ? '' : 'none';
        purchased.style.display = (tab === 'purchased') ? '' : 'none';
        trading.style.display = (tab === 'trading') ? '' : 'none';
      }

      tabs.forEach(btn => {
        btn.addEventListener('click', () => {
          tabs.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          show(btn.dataset.tab);
        });
      });

      // 初期表示：出品タブ
      show('selling');
    })();
  </script>
@endsection