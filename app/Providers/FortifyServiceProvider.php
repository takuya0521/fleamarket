<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\RegisteredUserController as FortifyRegisteredUserController;
use App\Http\Controllers\Auth\RegisteredUserController as AppRegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController as AppAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController as FortifyVerifyEmailController;
use App\Http\Controllers\Auth\VerifyEmailController as AppVerifyEmailController;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortifyの登録コントローラを差し替え（FormRequestでバリデーションするため）
        $this->app->bind(FortifyRegisteredUserController::class, AppRegisteredUserController::class);
        $this->app->bind(FortifyAuthenticatedSessionController::class, AppAuthenticatedSessionController::class);
        $this->app->bind(FortifyVerifyEmailController::class, AppVerifyEmailController::class);

        // Fortifyのログイン/登録画面
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        /**
         * メール認証画面（/email/verify） は “誘導画面” ではなく “認証画面” にする
         * 要件：誘導画面 → 認証画面へ遷移、を成立させるため
         */
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

        Fortify::redirects('register', '/verify/notice');
        Fortify::redirects('email-verification', '/mypage/profile');

        // Fortify Actions
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // RateLimiter
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())) . '|' . $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        /**
         * 登録直後は認証誘導画面へ
         * （要件：メール認証を実装した場合はメール認証画面へ遷移）
         * ※ここでは “誘導画面” を挟む設計なので /verify/notice に寄せる
         */
        Fortify::redirects('register', '/verify/notice');
    }
}
