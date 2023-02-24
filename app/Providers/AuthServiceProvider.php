<?php

namespace App\Providers;

use App\Utils\Constants;
use App\Utils\Grants\BackofficeGrant;
use Carbon\CarbonInterval;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->hasRole(Constants::ROLE_SUPER_ADMIN) ? true : null;
        });

        Passport::tokensExpireIn(now()->addMonths(7));
        Passport::refreshTokensExpireIn(now()->addMonths(7));
        Passport::personalAccessTokensExpireIn(now()->addMonths(7));

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeBackofficeGrant(), CarbonInterval::months(7)
        );
    }

    protected function makeBackofficeGrant()
    {
        $grant = new BackofficeGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
