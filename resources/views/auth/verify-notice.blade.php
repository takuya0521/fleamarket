@extends('layouts.app')
@section('title','メール認証のお願い')

@section('content')
  <div class="card">
    <h1 style="margin:0 0 8px;">メール認証が必要です</h1>
    <p style="color:#666; margin:0 0 14px;">
      登録したメールアドレス宛に認証メールを送信しました。メール内のリンクをクリックして認証を完了してください。
    </p>
    <form method="POST" action="{{ route('verification.send') }}" style="margin-top:12px;">
      @csrf
      <button class="btn" type="submit">認証メールを再送する</button>
    </form>

    @if (session('status') === 'verification-link-sent')
      <div style="margin-top:10px; color:#0a7;">認証メールを再送しました。</div>
    @endif
    <a class="btn primary" href="http://localhost:8026" target="_blank" rel="noopener">
        認証はこちらから（メールを確認）
    </a>

  </div>
@endsection
