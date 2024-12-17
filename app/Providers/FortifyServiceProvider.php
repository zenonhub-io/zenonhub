<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Auth\AuthenticateLoginAttempt;
use App\Actions\Auth\CreateNewUser;
use App\Actions\Profile\ResetUserPassword;
use App\Actions\Profile\UpdateUserDetails;
use App\Actions\Profile\UpdateUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;

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
        $this->configureLoginRedirect();
        $this->configureLogoutRedirect();

        Fortify::viewPrefix('auth.');
        Fortify::authenticateUsing([new AuthenticateLoginAttempt, '__invoke']);
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserDetails::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    }

    private function configureLoginRedirect(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {
                if ($request->input('redirect')) {
                    return redirect()->to($request->input('redirect'));
                }

                return redirect('/');
            }
        });
    }

    private function configureLogoutRedirect(): void
    {
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                if ($request->input('redirect')) {
                    return redirect()->to($request->input('redirect'));
                }

                return redirect('/');
            }
        });
    }
}
