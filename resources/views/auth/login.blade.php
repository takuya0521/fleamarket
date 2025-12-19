@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
  <div style="max-width:480px; margin:0 auto; background:#fff; border:1px solid #e5e5e5; border-radius:16px; padding:18px;">
    <h1 style="margin:0 0 14px;">ログイン</h1>

    <form method="POST" action="{{ url('/login') }}" novalidate>
      @csrf

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

      <div style="margin:12px 0;">
        <label style="display:flex; gap:8px; align-items:center; color:#555; font-size:13px;">
          <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          ログイン状態を保持する
        </label>
      </div>

      <button type="submit"
              style="width:100%; padding:12px; border:none; background:#111; color:#fff; border-radius:12px; cursor:pointer;">
        ログイン
      </button>
    </form>

    <p style="margin:14px 0 0; color:#666; font-size:13px;">
      アカウントがない場合は <a href="{{ url('/register') }}">会員登録</a>
    </p>
  </div>
@endsection
