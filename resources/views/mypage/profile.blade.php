@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
  <style>
    .panel { max-width:720px; margin:0 auto; background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px; }
    .row { display:flex; gap:14px; align-items:center; }
    .avatar { width:72px; height:72px; border-radius:999px; background:#eee; overflow:hidden; flex:0 0 auto; border:1px solid #e5e5e5; }
    .avatar img { width:100%; height:100%; object-fit:cover; display:block; }
    .field { margin-top:12px; }
    label { display:block; font-size:13px; color:#555; margin-bottom:6px; }
    input[type="text"], input[type="file"] { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:12px; }
    .btn { display:inline-flex; align-items:center; justify-content:center; height:40px; padding:0 14px; border-radius:12px; border:1px solid #111; background:#111; color:#fff; cursor:pointer; }
    .btn.ghost { background:#fff; color:#111; border-color:#ddd; text-decoration:none; }
    .err { background:#fff5f5; border:1px solid #ffd1d1; padding:12px; border-radius:12px; margin-bottom:12px; }
  </style>

  <div style="margin-bottom:12px;">
    <a href="{{ route('mypage.index') }}">← マイページへ戻る</a>
  </div>

  @if ($errors->any())
    <div class="err">
      <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $avatar = $user->profile_image_path ? \Illuminate\Support\Facades\Storage::url($user->profile_image_path) : '';
  @endphp

  <div class="panel">
    <h1 style="margin:0 0 14px;">プロフィール設定</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">
        <div class="avatar">
          @if($avatar)
            <img src="{{ $avatar }}" alt="profile">
          @endif
        </div>

        <div style="flex:1;">
          <label>プロフィール画像（任意）</label>
          <input type="file" name="profile_image" accept="image/*">
          <div style="color:#666; font-size:12px; margin-top:6px;">2MBまで / jpg,png等</div>
        </div>
      </div>

      <div class="field">
        <label>ユーザー名</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
      </div>

      <div class="field">
        <label>郵便番号（例: 123-4567）</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
      </div>

      <div class="field">
        <label>住所</label>
        <input type="text" name="address" value="{{ old('address', $user->address) }}">
      </div>

      <div class="field">
        <label>建物名（任意）</label>
        <input type="text" name="building" value="{{ old('building', $user->building) }}">
      </div>

      <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
        <button class="btn" type="submit">更新する</button>
        <a class="btn ghost" href="{{ route('mypage.index') }}">キャンセル</a>
      </div>
    </form>
  </div>
@endsection
