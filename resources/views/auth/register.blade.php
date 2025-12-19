@extends('layouts.app')

@section('title', '会員登録')

@section('content')
  <div style="max-width:480px; margin:0 auto; background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px;">
    <h1 style="margin:0 0 14px;">会員登録</h1>

    <form method="POST" action="{{ url('/register') }}" novalidate>
      @csrf

      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">ユーザー名</label>
        <input type="text" name="name" value="{{ old('name') }}"
               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">

        @error('name')
          <p style="margin:6px 0 0; color:#e11d48; font-size:12px;">{{ $message }}</p>
        @enderror
      </div>

      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">メールアドレス</label>
        <input type="email" name="email" value="{{ old('email') }}"
               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">

        @error('email')
          <p style="margin:6px 0 0; color:#e11d48; font-size:12px;">{{ $message }}</p>
        @enderror
      </div>

      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">パスワード</label>
        <input type="password" name="password"
               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">

        @error('password')
          <p style="margin:6px 0 0; color:#e11d48; font-size:12px;">{{ $message }}</p>
        @enderror
      </div>

      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">確認用パスワード</label>
        <input type="password" name="password_confirmation"
               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">

        @error('password_confirmation')
          <p style="margin:6px 0 0; color:#e11d48; font-size:12px;">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit"
              style="width:100%; padding:12px; border:none; background:#111; color:#fff; border-radius:12px; cursor:pointer;">
        登録する
      </button>
    </form>

    <p style="margin:14px 0 0; color:#666; font-size:13px;">
      すでにアカウントがある場合は <a href="{{ url('/login') }}">ログイン</a>
    </p>
  </div>
@endsection
