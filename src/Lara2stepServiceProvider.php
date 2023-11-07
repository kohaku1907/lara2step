<?php

namespace Kohaku1907\Lara2step;

use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class Lara2stepServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('2step')
            ->hasConfigFile('2step')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_2step_auth_table')
            ->hasRoute('web');
    }

    public function bootingPackage(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('2step', Http\Middleware\ConfirmTwoStepVerification::class);
    }
}
