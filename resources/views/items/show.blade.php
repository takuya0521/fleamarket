@extends('layouts.app')

@section('title', $item->name)

@php
  $sold = !is_null($item->purchase ?? null);
@endphp

@section('content')
  <style>
    .detail { display:grid; grid-template-columns: 520px 1fr; gap:24px; align-items:start; }
    @media (max-width: 980px){ .detail{ grid-template-columns:1fr; } }

    .panel { background:#fff; border:1px solid #e5e5e5; border-radius:16px; overflow:hidden; }
    .thumb { position:relative; aspect-ratio: 1 / 1; background:#eee; }
    .thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .sold { position:absolute; top:12px; left:12px; background:rgba(0,0,0,.75); color:#fff; padding:7px 12px; border-radius:999px; font-size:12px; }

    .box { padding:16px; }
    .title { margin:0; font-size:20px; font-weight:800; }
    .brand { margin-top:6px; color:#666; font-size:13px; }
    .price { margin-top:12px; font-size:20px; font-weight:800; }
    .badges { margin-top:12px; display:flex; flex-wrap:wrap; gap:8px; }
    .badge { font-size:12px; padding:6px 10px; border:1px solid #ddd; border-radius:999px; background:#fff; color:#111; }
    .desc { margin-top:12px; color:#333; line-height:1.7; }

    .actions { display:flex; gap:10px; align-items:center; margin-top:14px; }
    .likebtn { border:1px solid #ddd; background:#fff; border-radius:12px; padding:10px 12px; cursor:pointer; }
    .counts { color:#666; font-size:13px; display:flex; gap:10px; align-items:center; }

    .commentForm textarea{
      width:100%;
      min-height:90px;
      padding:10px 12px;
      border:1px solid #ddd;
      border-radius:12px;
      resize:vertical;
    }
    .commentForm button{
      margin-top:10px;
      padding:10px 12px;
      border:1px solid #111;
      background:#111;
      color:#fff;
      border-radius:12px;
      cursor:pointer;
    }
    .cList{ margin-top:14px; display:flex; flex-direction:column; gap:10px; }
    .cItem{ border:1px solid #eee; border-radius:14px; padding:12px; background:#fafafa; }
    .cHead{ display:flex; gap:10px; align-items:center; color:#666; font-size:12px; }
    .cBody{ margin-top:6px; color:#111; white-space:pre-wrap; }
  </style>

  <div style="margin-bottom:12px;">
    <a href="{{ route('items.index') }}">← 一覧へ戻る</a>
  </div>

  @auth
    <div style="color:#666; font-size:12px; margin-bottom:8px;">
      login: {{ auth()->user()->email }} / seller_id: {{ $item->seller_id }} / my_id: {{ auth()->id() }}
    </div>
  @endauth

  <div class="detail">
    <div class="panel">
      <div class="thumb">
        @if($item->image_url)
          <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
        @endif
        @if($sold)
          <div class="sold">Sold</div>
        @endif
      </div>
    </div>

    <div class="panel">
      <div class="box">
        <h1 class="title">{{ $item->name }}</h1>

        <div class="brand">
          ブランド：{{ filled($item->brand) ? $item->brand : 'なし' }} / 状態：{{ $item->condition }}
        </div>

        <div class="price">¥{{ number_format($item->price) }}</div>

        <div class="badges">
          @foreach($item->categories as $cat)
            <span class="badge">{{ $cat->name }}</span>
          @endforeach
        </div>

        <div class="actions">
          @auth
            @if($item->seller_id === auth()->id() && !$sold)
              <a class="likebtn"
                 href="{{ route('sell.edit', $item) }}"
                 style="text-decoration:none; color:#111;">
                編集
              </a>

              <form method="POST"
                    action="{{ route('sell.destroy', $item) }}"
                    style="margin:0;"
                    onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                <button class="likebtn" type="submit">削除</button>
              </form>
            @endif
          @endauth

          @auth
            <form method="POST" action="{{ route('items.like', $item) }}" style="margin:0;">
              @csrf
              <button class="likebtn" type="submit">{{ $liked ? '♥ いいね済み' : '♡ いいね' }}</button>
            </form>
          @endauth

          <div class="counts">
            <span>❤ {{ $item->likes_count }}</span>
            <span>💬 {{ $item->comments_count }}</span>
          </div>

          @if(!$sold && $item->seller_id !== (auth()->id() ?? -1))
            @auth
              <a class="likebtn" href="{{ route('purchase.show', $item) }}" style="text-decoration:none; color:#111;">
                購入する
              </a>
            @else
              <a class="likebtn" href="{{ url('/login') }}" style="text-decoration:none; color:#111;">
                ログインして購入
              </a>
            @endauth
          @endif
        </div>

        <div class="desc">{{ $item->description }}</div>

        <hr style="margin:16px 0; border:none; border-top:1px solid #eee;">

        <h2 style="margin:0 0 10px; font-size:16px;">
          コメント（{{ $item->comments_count ?? $item->comments->count() }}件）
        </h2>

        @auth
          <form class="commentForm" method="POST" action="{{ route('items.comment', $item) }}" novalidate>
            @csrf
            <textarea name="body" maxlength="255" placeholder="コメントを入力（255文字以内）">{{ old('body') }}</textarea>

            @error('body')
              <p style="color:#e60023; font-size:13px; margin:8px 0 0;">{{ $message }}</p>
            @enderror

            <button type="submit">コメントを送信</button>
          </form>
        @else
          <div style="color:#666; font-size:13px;">
            コメントするには <a href="{{ url('/login') }}">ログイン</a> が必要です。
          </div>
        @endauth

        <div class="cList">
          @forelse($item->comments as $comment)
            <div class="cItem">
              <div class="cHead">
                <span>{{ $comment->user->name ?? 'unknown' }}</span>
                <span>・</span>
                <span>{{ $comment->created_at?->format('Y/m/d H:i') }}</span>
              </div>
              <div class="cBody">{{ $comment->body }}</div>
            </div>
          @empty
            <div style="color:#666; font-size:13px;">まだコメントはありません。</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
