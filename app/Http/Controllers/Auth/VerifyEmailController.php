<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Routing\Controller;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        // 認証完了（verified_atが入る）
        $request->fulfill();

        // 認証後はプロフィール設定へ固定
        return redirect()->route('profile.edit')->with('status', 'verified');
        // routeが無いなら: return redirect('/mypage/profile')->with('status','verified');
    }
}
