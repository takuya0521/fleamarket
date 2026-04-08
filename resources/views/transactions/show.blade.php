@extends('layouts.app')

@section('title', '取引チャット')

@section('content')
@php
  $item = $purchase->item;
  $partner = $isBuyer ? ($item?->seller) : $purchase->buyer;

  $partnerName = $partner?->name ?? ($partner?->email ?? 'ユーザー');
  $buyerCompleted = !is_null($purchase->buyer_completed_at ?? null);

  // 画像URL（Itemに image_url があるなら優先）
  $itemImg = '';
  if ($item) {
    if (!empty($item->image_url)) {
      $itemImg = $item->image_url;
    } else {
      $p = (string)($item->image_path ?? '');
      if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) {
        $itemImg = $p;
      } elseif (\Illuminate\Support\Str::startsWith($p, 'public/')) {
        $itemImg = asset('storage/' . \Illuminate\Support\Str::after($p, 'public/'));
      } elseif ($p !== '') {
        $itemImg = \Illuminate\Support\Facades\Storage::url($p);
      }
    }
  }
@endphp

<style>
  /* 画面全体 */
  .txpage {
    max-width: 1200px;
    margin: 0 auto;
    padding: 18px 14px;
  }

  .txlayout {
    display: grid;
    grid-template-columns: 260px minmax(0, 1fr);
    gap: 0;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
  }

  /* 左：その他の取引 */
  .txside {
    background: #9ca3af;
    padding: 16px 14px;
  }

  .txside-title {
    color: #fff;
    font-weight: 800;
    font-size: 18px;
    margin-bottom: 12px;
  }

  .txside-list {
    display: grid;
    gap: 12px;
  }

  .txside-item {
    position: relative; /* ←未読バッジ配置のため */
    display: grid;
    grid-template-columns: 52px minmax(0, 1fr);
    gap: 12px;
    align-items: center;
    padding: 14px 12px;
    border-radius: 14px;
    text-decoration: none;
    color: #111;
    background: rgba(255,255,255,.88);
    border: 1px solid rgba(255,255,255,.65);
  }
  .txside-item:hover { background: rgba(255,255,255,.96); }

  /* 選択中 */
  .txside-item.active { outline: 2px solid #111; }

  /* 未読あり（枠を赤寄せ） */
  .txside-item.has-unread { border-color: #ef4444; }

  .txside-unread {
    position: absolute;
    top: 10px;
    left: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
  }

  .txside-thumb {
    width: 52px; height: 52px;
    border-radius: 999px;
    overflow: hidden;
    background: #e5e7eb;
    flex: 0 0 auto;
  }
  .txside-thumb img { width:100%; height:100%; object-fit: cover; display:block; }

  .txside-name {
    font-weight: 800;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* 右：メイン */
  .txmain {
    display: grid;
    grid-template-rows: auto auto 1fr auto;
    min-height: 640px;
  }

  /* ヘッダー */
  .txheader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #e5e5e5;
    background: #fff;
  }

  .txheader-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .txavatar {
    width: 44px; height: 44px;
    border-radius: 999px;
    background: #e5e5e7eb;
    flex: 0 0 auto;
  }

  .txheader-title {
    font-weight: 900;
    font-size: 18px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .btn-complete {
    height: 34px;
    padding: 0 14px;
    border-radius: 999px;
    border: 0;
    background: #fb7185;
    color: #fff;
    font-weight: 800;
    cursor: pointer;
  }
  .btn-complete:disabled { opacity: .5; cursor: not-allowed; }

  .chip {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height: 28px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid #ddd;
    background:#fff;
    font-size: 12px;
    font-weight: 700;
    color:#111;
  }

  /* 商品情報ブロック */
  .txproduct {
    display: grid;
    grid-template-columns: 160px minmax(0,1fr);
    gap: 16px;
    padding: 16px;
    border-bottom: 1px solid #e5e5e5;
    background: #fff;
  }

  .txproduct-img {
    width: 160px; height: 160px;
    border: 1px solid #e5e5e5;
    background: #f3f4f6;
    border-radius: 6px;
    overflow: hidden;
  }
  .txproduct-img img { width:100%; height:100%; object-fit: cover; display:block; }

  .txproduct-name { font-weight: 900; font-size: 28px; margin-top: 6px; }
  .txproduct-price { font-size: 20px; margin-top: 10px; color:#111; }

  .txproduct-links { margin-top: 12px; }
  .txproduct-links a { text-decoration:none; color:#2563eb; font-size: 13px; }

  /* メッセージエリア */
  .txmessages {
    padding: 18px 16px;
    background: #fff;
    overflow: auto;
  }

  .msgrow {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
  }
  .msgrow.mine { justify-content: flex-end; }

  .msgavatar {
    width: 36px; height: 36px;
    border-radius: 999px;
    background: #e5e7eb;
    flex: 0 0 auto;
  }
  .msgrow.mine .msgavatar { order: 2; }

  .msgbody {
    max-width: 520px;
    min-width: 220px;
  }
  .msgrow.mine .msgbody { text-align: right; }

  .msgname {
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 6px;
    color: #111;
  }

  .msgbubble {
    background: #e5e7eb;
    border-radius: 6px;
    padding: 12px 14px;
    line-height: 1.6;
    font-size: 14px;
    white-space: pre-wrap;
    word-break: break-word;
  }
  .msgrow.mine .msgbubble {
    background: #d1d5db;
  }

  .msgimage {
    margin-top: 10px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e5e5e5;
    background: #f3f4f6;
  }
  .msgimage img { width:100%; height:auto; display:block; max-height: 260px; object-fit: contain; }

  .msgmeta {
    margin-top: 6px;
    font-size: 11px;
    color: #6b7280;
    display: flex;
    gap: 10px;
    justify-content: flex-start;
  }
  .msgrow.mine .msgmeta { justify-content: flex-end; }

  .msgactions {
    margin-top: 6px;
    font-size: 12px;
    color:#6b7280;
    display:flex;
    gap: 10px;
    justify-content: flex-end;
  }

  /* 下部入力 */
  .txfooter {
    padding: 14px 16px;
    border-top: 1px solid #e5e5e5;
    background: #fff;
  }

  .txform {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 12px;
    align-items: center;
  }

  .txinput {
    height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 0 12px;
    font: inherit;
  }

  .btn-image {
    height: 40px;
    padding: 0 14px;
    border-radius: 10px;
    border: 2px solid #fb7185;
    background: #fff;
    color: #fb7185;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
  }

  .btn-send {
    width: 46px;
    height: 40px;
    border: none;
    background: transparent;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .txerrors { margin: 10px 0 0; display: grid; gap: 8px; }
  .txerror {
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #991b1b;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 13px;
  }

  .txhint { margin-top: 8px; color:#6b7280; font-size: 12px; }

  @media (max-width: 980px) {
    .txlayout { grid-template-columns: 1fr; }
    .txside { border-bottom: 1px solid rgba(255,255,255,.6); }
    .txmain { min-height: auto; }
  }
</style>

<div class="txpage">
  <div class="txlayout">
    {{-- 左：その他の取引 --}}
    <aside class="txside">
      <div class="txside-title">その他の取引</div>

      <div class="txside-list">
        @foreach($sidebarPurchases as $sidePurchase)
          @php
            $sideItem = $sidePurchase->item;
            $active = $sidePurchase->id === $purchase->id;
            $unread = (int)($sidePurchase->unread_count ?? 0);

            $sideImg = '';
            if ($sideItem) {
              if (!empty($sideItem->image_url)) {
                $sideImg = $sideItem->image_url;
              } else {
                $p = (string)($sideItem->image_path ?? '');
                if (\Illuminate\Support\Str::startsWith($p, ['http://','https://'])) {
                  $sideImg = $p;
                } elseif (\Illuminate\Support\Str::startsWith($p, 'public/')) {
                  $sideImg = asset('storage/' . \Illuminate\Support\Str::after($p, 'public/'));
                } elseif ($p !== '') {
                  $sideImg = \Illuminate\Support\Facades\Storage::url($p);
                }
              }
            }
          @endphp

          <a href="{{ route('transactions.show', $sidePurchase) }}"
             class="txside-item {{ $active ? 'active' : '' }} {{ $unread > 0 ? 'has-unread' : '' }}">
            <div class="txside-thumb">
              @if($sideImg)
                <img src="{{ $sideImg }}" alt="{{ $sideItem?->name ?? '商品' }}">
              @endif
            </div>

            <div class="txside-name">{{ $sideItem?->name ?? '商品' }}</div>

            @if($unread > 0)
              <span class="txside-unread">{{ $unread }}</span>
            @endif
          </a>
        @endforeach
      </div>
    </aside>

    {{-- 右：メイン --}}
    <main class="txmain">
      {{-- ヘッダー --}}
      <header class="txheader">
        <div class="txheader-left">
          <div class="txavatar"></div>
          <div class="txheader-title">「{{ $partnerName }}」さんとの取引画面</div>
        </div>

        <div>
          @if($isBuyer)
            @if(!$buyerRated)
              <button type="button" class="btn-complete" data-open-modal="buyer-complete-modal">取引を完了する</button>
            @else
              <span class="chip">評価済み</span>
            @endif
          @elseif($isSeller)
            @if(!$buyerCompleted)
              <span class="chip">購入者の完了待ち</span>
            @elseif(!$sellerRated)
              <button type="button" class="btn-complete" data-open-modal="seller-rate-modal">購入者を評価する</button>
            @else
              <span class="chip">評価済み</span>
            @endif
          @endif
        </div>
      </header>

      {{-- 商品情報 --}}
      <section class="txproduct">
        <div class="txproduct-img">
          @if($itemImg)
            <img src="{{ $itemImg }}" alt="{{ $item?->name ?? '商品' }}">
          @endif
        </div>

        <div>
          <div class="txproduct-name">{{ $item?->name ?? '商品名' }}</div>
          <div class="txproduct-price">¥{{ number_format((int)($item?->price ?? 0)) }}</div>

          @if($item)
            <div class="txproduct-links">
              <a href="{{ route('items.show', $item) }}">商品詳細を見る →</a>
            </div>
          @endif
        </div>
      </section>

      {{-- メッセージ --}}
      <section class="txmessages" id="messages-box">
        @forelse($messages as $message)
          @php $mine = (int)auth()->id() === (int)$message->sender_id; @endphp

          <div class="msgrow {{ $mine ? 'mine' : '' }}">
            <div class="msgavatar"></div>

            <div class="msgbody">
              <div class="msgname">{{ $message->sender->name ?? 'ユーザー名' }}</div>

              <div class="msgbubble">{{ $message->body }}</div>

              @if(!empty($message->image_path))
                <div class="msgimage">
                  <img src="{{ $message->image_url }}" alt="添付画像">
                </div>
              @endif

              <div class="msgmeta">
                <span>{{ optional($message->created_at)->format('Y/m/d H:i') }}</span>
                @if(!is_null($message->edited_at))
                  <span>編集済み</span>
                @endif
              </div>

              @if($mine)
                <div class="msgactions">
                  <span>編集</span>
                  <span>削除</span>
                </div>
              @endif
            </div>
          </div>
        @empty
          <div style="color:#6b7280;">まだメッセージはありません。</div>
        @endforelse
      </section>

      {{-- 送信フォーム --}}
      <footer class="txfooter">
        @if($errors->any())
          <div class="txerrors">
            @foreach($errors->all() as $error)
              <div class="txerror">{{ $error }}</div>
            @endforeach
          </div>

          {{-- エラー時：入力欄位置へ --}}
          <script>window.location.hash = 'message-form';</script>
        @endif

        <form method="POST"
              action="{{ route('transactions.messages.store', $purchase) }}"
              enctype="multipart/form-data"
              class="txform"
              id="message-form">
          @csrf

          <input
            class="txinput"
            type="text"
            name="body"
            id="tx-body"
            maxlength="400"
            placeholder="取引メッセージを記入してください"
            value="{{ old('body') }}"
            data-draft-key="tx_draft_u{{ auth()->id() }}_p{{ $purchase->id }}"
          >

          <label class="btn-image">
            画像を追加
            <input type="file" name="image" id="tx-image" hidden accept=".png,.jpeg,image/png,image/jpeg">
          </label>

          <button class="btn-send" type="submit" aria-label="送信">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 11.5L21 3L12.5 21L11 13L3 11.5Z" stroke="#111" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
          </button>
        </form>

        <div class="txhint">画像は .png または .jpeg のみアップロードできます。本文は必須（400文字以内）です。</div>
      </footer>
    </main>
  </div>
</div>

@if($isBuyer && !$buyerRated)
  <div class="modal-backdrop" id="buyer-complete-modal" aria-hidden="true" style="display:none;"></div>
@endif

@if($isSeller && $buyerCompleted && !$sellerRated)
  <div class="modal-backdrop" id="seller-rate-modal" aria-hidden="true" style="display:none;"></div>
@endif

<script>
  (function () {
    const box = document.getElementById('messages-box');

    function scrollToBottom() {
      if (!box) return;
      box.scrollTop = box.scrollHeight;
    }

    // 表示直後・画像読み込み後にも下に寄せる（送信後に上へ戻る対策）
    scrollToBottom();
    document.addEventListener('DOMContentLoaded', () => {
      scrollToBottom();
      setTimeout(scrollToBottom, 50);
      setTimeout(scrollToBottom, 200);
    });
    window.addEventListener('load', () => {
      scrollToBottom();
      setTimeout(scrollToBottom, 50);
      setTimeout(scrollToBottom, 200);
    });

    // ✅ 下書き保持（本文のみ / purchase単位）
    const input = document.getElementById('tx-body');
    if (input) {
      const key = input.getAttribute('data-draft-key') || 'tx_draft_default';

      // 送信成功時だけ下書きを削除（バリデーションエラー時は残す）
      @if(session('message_sent'))
        try { localStorage.removeItem(key); } catch (e) {}
      @endif

      // old(body) がある場合は old を優先、無ければ localStorage から復元
      const oldBody = @json(old('body', ''));
      if (!oldBody) {
        try {
          const saved = localStorage.getItem(key);
          if (saved && !input.value) input.value = saved;
        } catch (e) {}
      }

      // 入力中は間引いて保存
      let timer = null;
      input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          try { localStorage.setItem(key, input.value); } catch (e) {}
        }, 150);
      });

      // 画面遷移直前にも保存（念のため）
      window.addEventListener('beforeunload', () => {
        try { localStorage.setItem(key, input.value); } catch (e) {}
      });
    }

    // モーダル（既存で使ってる場合だけ）
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-open-modal');
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'flex';
      });
    });
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-close-modal');
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
      });
    });
  })();
</script>
@endsection