@extends('layouts.app')

@section('title', '商品一覧')

@php
  $isMylist = ($tab ?? 'all') === 'mylist';
@endphp

@section('content')
  <style>
    .grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:14px; }
    @media (max-width: 980px) { .grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 720px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    .card { display:block; text-decoration:none; color:#111; background:#fff; border:1px solid #e5e5e5; border-radius:16px; overflow:hidden; }
    .thumb { position:relative; aspect-ratio: 1 / 1; background:#eee; }
    .thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .sold { position:absolute; top:10px; left:10px; background:rgba(0,0,0,.75); color:#fff; padding:6px 10px; border-radius:999px; font-size:12px; }
    .body { padding:12px 12px 14px; }
    .name { font-weight:700; line-height:1.3; }
    .meta { margin-top:8px; color:#666; font-size:12px; display:flex; gap:10px; }
    .empty { padding:26px; background:#fff; border:1px solid #e5e5e5; border-radius:16px; color:#666; }
    .pager { margin-top:18px; }
  </style>

  @if($isMylist && !auth()->check())
    <div class="empty">マイリストはログイン後に表示されます。</div>
  @else
    @if($items instanceof \Illuminate\Support\Collection)
      @if($items->isEmpty())
        <div class="empty">表示する商品がありません。</div>
      @else
        <div class="grid">
          @foreach($items as $item)
            @include('items.partials.card', ['item' => $item])
          @endforeach
        </div>
      @endif
    @else
      @if($items->count() === 0)
        <div class="empty">該当する商品がありません。</div>
      @else
        <div class="grid">
          @foreach($items as $item)
            @include('items.partials.card', ['item' => $item])
          @endforeach
        </div>

        <div class="pager">
          {{ $items->links() }}
        </div>
      @endif
    @endif
  @endif
@endsection
