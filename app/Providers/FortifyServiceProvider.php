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
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Personnaliser la réponse de connexion pour rediriger vers le panier si nécessaire
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // Si une réservation a été ajoutée au panier, rediriger vers le panier
                if (session('redirect_to_cart')) {
                    session()->forget('redirect_to_cart');
                    return redirect()->intended('/panier')->with('success', 'Votre réservation a été ajoutée au panier !');
                }
                
                return redirect()->intended(config('fortify.home'));
            }
        });

        // Personnaliser la réponse d'inscription pour rediriger vers le panier si nécessaire
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                // Si une réservation a été ajoutée au panier, rediriger vers le panier
                if (session('redirect_to_cart')) {
                    session()->forget('redirect_to_cart');
                    return redirect('/panier')->with('success', 'Votre compte a été créé et votre réservation a été ajoutée au panier !');
                }
                
                return redirect(config('fortify.home'));
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
