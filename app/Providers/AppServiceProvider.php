<?php

namespace App\Providers;

use App\Services\CustomerService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\AttributeService;

class AppServiceProvider extends ServiceProvider
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
        // Force use of https
        URL::forceScheme('https');
        //Paginator::useBootstrapFive();

        View::composer('customers.show', function ($view) {
            $view->with(['attributeService' => new AttributeService()]);
        });

        View::composer('customers.show', function ($view) {
            $view->with(['customerService' => new CustomerService()]);
        });

        View::composer('customers.edit', function ($view) {
            $view->with(['customerService' => new CustomerService()]);
        });

        View::composer('registrations.create', function ($view) {
            $view->with(['attributeService' => new AttributeService()]);
        });

        View::composer('registrations.create', function ($view) {
            $view->with(['customerService' => new CustomerService()]);
        });

/*
        View::composer('store', function ($view) {
            $view->with(['attributeService' => new AttributeService()]);
        });
*/

/*
        Blade::directive('attributes', function ($customer) {
            return "<?php echo app(App\Services\AttributeService::class)->currentServices($customer); ?>";
        });
*/

    }
}
