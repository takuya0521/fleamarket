<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'coachtechフリマ')</title>

  <style>
    :root{
      --bg:#f6f6f6;
      --card:#ffffff;
      --text:#111;
      --muted:#666;
      --border:#e5e5e5;
      --primary:#111;
      --radius:14px;
    }

    *{ box-sizing:border-box; }
    body{
      font-family: system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans JP", sans-serif;
      margin:0;
      background:var(--bg);
      color:var(--text);
    }

    header{
      background:var(--card);
      border-bottom:1px solid var(--border);
    }

    .wrap{
      max-width:1200px;
      margin:0 auto;
      padding:16px 18px;
    }

    .topbar{
      display:flex;
      align-items:center;
      gap:14px;
    }

    .brand{
      font-weight:800;
      text-decoration:none;
      color:var(--text);
      letter-spacing:.02em;
      font-size:18px;
      white-space:nowrap;
    }

    .search{
      flex:1;
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:center;
    }
    .search input{
      width:100%;
      max-width:520px;
      padding:11px 12px;
      border:1px solid #ddd;
      border-radius:12px;
      background:#fff;
      outline:none;
    }
    .search input:focus{
      border-color:#bbb;
    }
    .search button{
      padding:11px 14px;
      border:1px solid var(--primary);
      background:var(--primary);
      color:#fff;
      border-radius:12px;
      cursor:pointer;
      white-space:nowrap;
    }

    .actions{
      display:flex;
      gap:10px;
      align-items:center;
      margin-left:auto;
      white-space:nowrap;
    }
    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      height:38px;
      padding:0 12px;
      border-radius:12px;
      border:1px solid #ddd;
      background:#fff;
      color:var(--text);
      text-decoration:none;
      cursor:pointer;
      font-size:14px;
    }
    .btn.primary{
      background:var(--primary);
      border-color:var(--primary);
      color:#fff;
    }

    .tabs{
      display:flex;
      gap:10px;
      margin-top:12px;
      padding-top:12px;
      border-top:1px solid var(--border);
    }
    .tab{
      padding:8px 14px;
      border-radius:999px;
      border:1px solid #ddd;
      background:#fff;
      text-decoration:none;
      color:var(--text);
      font-size:14px;
    }
    .tab.active{
      background:var(--primary);
      color:#fff;
      border-color:var(--primary);
    }

    main .wrap{
      padding-top:22px;
      padding-bottom:60px;
    }

    /* レスポンシブ：狭いときは縦に積む */
    @media (max-width: 820px){
      .topbar{ flex-wrap:wrap; }
      .search{ order:3; width:100%; justify-content:flex-start; }
      .search input{ max-width:none; }
      .actions{ order:2; margin-left:0; }
    }
  </style>

  @stack('head')
</head>
<body>
<header>
  <div class="wrap">
    <div class="topbar">
      <a class="brand" href="{{ route('items.index') }}">coachtechフリマ</a>

      <form class="search" method="GET" action="{{ route('items.index') }}">
        <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="商品名で検索">
        @if(($tab ?? 'all') === 'mylist')
          <input type="hidden" name="tab" value="mylist">
        @endif
        <button type="submit">検索</button>
      </form>

      <div class="actions">
        @auth
            <a href="{{ route('mypage.index') }}" class="btn">マイページ</a>
            <a href="{{ route('sell.create') }}" class="btn">出品</a>
        @endauth
        @auth
          <form method="POST" action="{{ url('/logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn">ログアウト</button>
          </form>
        @else
          <a href="{{ url('/login') }}" class="btn">ログイン</a>
          <a href="{{ url('/register') }}" class="btn primary">会員登録</a>
        @endauth
      </div>
    </div>

    <nav class="tabs">
      <a class="tab {{ ($tab ?? 'all') === 'all' ? 'active' : '' }}"
         href="{{ route('items.index', array_filter(['keyword' => $keyword ?? null])) }}">
        おすすめ
      </a>

      <a class="tab {{ ($tab ?? 'all') === 'mylist' ? 'active' : '' }}"
         href="{{ route('items.index', array_filter(['tab' => 'mylist', 'keyword' => $keyword ?? null])) }}">
        マイリスト
      </a>
    </nav>
  </div>
</header>

<main>
  <div class="wrap">
    @yield('content')
  </div>
</main>
</body>
</html>
