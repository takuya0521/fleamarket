@extends('layouts.app')
@section('title','メール認証')

@section('content')
  <div class="card">
    <h1 style="margin:0 0 8px;">メール認証</h1>
    <p style="color:#666; margin:0 0 14px;">
      登録したメールアドレス宛に届いたリンクをクリックして認証を完了してください。
    </p>

    <form method="POST" action="{{ route('verification.send') }}" style="margin-top:12px;">
      @csrf
      <button class="btn" type="submit">認証メールを再送する</button>
    </form>

    @if (session('status') === 'verification-link-sent')
      <div style="margin-top:10px; color:#0a7;">認証メールを再送しました。</div>
    @endif
  </div>
@endsection
