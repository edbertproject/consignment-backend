<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserTokenRepository::class, \App\Repositories\UserTokenRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TestRepository::class, \App\Repositories\TestRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PartnerRepository::class, \App\Repositories\PartnerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CourierRepository::class, \App\Repositories\CourierRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProvinceRepository::class, \App\Repositories\ProvinceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CityRepository::class, \App\Repositories\CityRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DistrictRepository::class, \App\Repositories\DistrictRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserAddressRepository::class, \App\Repositories\UserAddressRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductCategoryRepository::class, \App\Repositories\ProductCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductRepository::class, \App\Repositories\ProductRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CartRepository::class, \App\Repositories\CartRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InvoiceRepository::class, \App\Repositories\InvoiceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentMethodRepository::class, \App\Repositories\PaymentMethodRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentMethodInstructionRepository::class, \App\Repositories\PaymentMethodInstructionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApiExternaLogRepository::class, \App\Repositories\ApiExternaLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApiExternalLogRepository::class, \App\Repositories\ApiExternalLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductBidRepository::class, \App\Repositories\ProductBidRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductParticipantRepository::class, \App\Repositories\ProductParticipantRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WishlistRepository::class, \App\Repositories\WishlistRepositoryEloquent::class);
        //:end-bindings:
    }
}
