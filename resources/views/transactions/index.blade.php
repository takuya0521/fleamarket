@extends('layouts.app')

@section('title', '取引チャット一覧')

@section('content')
  <style>
    .tx-wrap { display:grid; gap:14px; }
    .tx-card {
      background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:14px;
    }
    .tx-head {
      display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:4px;
    }
    .tx-title { font-size:20px; font-weight:800; }
    .tx-link {
      display:inline-flex; align-items:center; justify-content:center;
      height:38px; padding:0 12px; border-radius:12px;
      border:1px solid #ddd; background:#fff; color:#111; text-decoration:none;
    }

    .tx-list { display:grid; gap:12px; }
    .tx-row {
      display:grid;
      grid-template-columns: 92px minmax(0, 1fr) auto;
      gap:12px;
      align-items:center;
      padding:12px;
      border:1px solid #e5e5e5;
      border-radius:14px;
      background:#fff;
      text-decoration:none;
      color:#111;
    }
    .tx-row:hover { border-color:#cfcfcf; }

    .tx-thumb {
      width:92px; aspect-ratio:1/1;
      border-radius:12px; overflow:hidden; background:#eee; border:1px solid #eee;
    }
    .tx-thumb img { width:100%; height:100%; object-fit:cover; display:block; }

    .tx-main { min-width:0; }
    .tx-item-name {
      font-weight:800; font-size:16px; line-height:1.4;
      white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .tx-meta {
      margin-top:6px; color:#666; font-size:13px;
      display:flex; flex-wrap:wrap; gap:8px 12px; align-items:center;
    }
    .tx-badge {
      display:inline-flex; align-items:center; justify-content:center;
      min-width:24px; height:24px; padding:0 8px;
      border-radius:999px; background:#111; color:#fff; font-weight:700; font-size:12px;
    }
    .tx-badge.light {
      background:#f3f3f3; color:#333; border:1px solid #e5e5e5;
    }

    .tx-side { display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
    .tx-date { color:#666; font-size:12px; white-space:nowrap; }
    .tx-unread {
      display:inline-flex; align-items:center; justify-content:center;
      min-width:24px; height:24px; padding:0 8px;
      border-radius:999px; background:#ef4444; color:#fff; font-weight:700; font-size:12px;
    }

    .empty {
      padding:18px; border:1px solid #e5e5e5; border-radius:14px; background:#fff; color:#666;
    }
    .pager { margin-top:8px; }

    @media (max-width: 720px) {
      .tx-row {
        grid-template-columns: 72px minmax(0, 1fr);
      }
      .tx-thumb { width:72px; }
      .tx-side {
        grid-column: 2;
        align-items:flex-start;
        flex-direction:row;
        justify-content:space-between;
      }
    }
  </style>

  <div class="tx-wrap">
    <div class="tx-card">
      <div class="tx-head">
        <div class="tx-title">取引チャット一覧</div>
        <a href="{{ route('mypage.index') }}" class="tx-link">マイページへ戻る</a>
      </div>
      <div style="color:#666; font-size:13px;">
        最新メッセージのある取引順で表示しています。未読メッセージがある取引には赤いバッジが表示されます。
      </div>
    </div>

    @if($purchases->count() === 0)
      <div class="empty">
        取引中の商品はまだありません。
      </div>
    @else
      <div class="tx-list">
        @foreach($purchases as $purchase)
          @php
            $item = $purchase->item;
            $isBuyer = auth()->id() === $purchase->buyer_id;
            $partner = $isBuyer ? ($item?->seller) : $purchase->buyer;
            $unreadCount = (int) ($purchase->unread_count ?? 0);

            $rawLast = $purchase->last_message_at ?? null;
            $lastMessageAt = $rawLast ? \Illuminate\Support\Carbon::parse($rawLast) : null;
            $fallbackAt = $purchase->purchased_at ?? $purchase->created_at;
            $displayAt = $lastMessageAt ?? $fallbackAt;

            $statusText = is_null($purchase->buyer_completed_at ?? null) ? '取引中' : '購入者完了';
          @endphp

          <a href="{{ route('transactions.show', $purchase) }}" class="tx-row">
            <div class="tx-thumb">
              @if($item?->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
              @endif
            </div>

            <div class="tx-main">
              <div class="tx-item-name">{{ $item?->name ?? '商品情報なし' }}</div>
              <div class="tx-meta">
                <span>相手：{{ $partner?->name ?? ($partner?->email ?? '—') }}</span>
                <span class="tx-badge light">{{ $isBuyer ? '購入者' : '出品者' }}</span>
                <span class="tx-badge light">{{ $statusText }}</span>
              </div>
            </div>

            <div class="tx-side">
              <div class="tx-date">
                {{ $displayAt ? $displayAt->format('Y/m/d H:i') : '—' }}
              </div>
              @if($unreadCount > 0)
                <div class="tx-unread">{{ $unreadCount }}</div>
              @endif
            </div>
          </a>
        @endforeach
      </div>

      <div class="pager">
        {{ $purchases->links() }}
      </div>
    @endif
  </div>
@endsection