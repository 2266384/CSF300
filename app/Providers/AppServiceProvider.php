<?php

namespace App\Providers;

use App\Services\CustomerService;
use App\Services\TokenService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\AttributeService;
use Illuminate\Validation\Rules\Password;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TokenService::class, function ($app) {
            return new TokenService();
        });

        // Bind the PSR HTTP Client to Guzzle
        $this->app->bind(ClientInterface::class, function () {
            return new Client();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force use of https
        URL::forceScheme('https');
        //Paginator::useBootstrapFive();

        // Set the defaults for password validation
        // Minimum 8 characters, mixed case at least one letter, number, and symbol
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->letters()
                ->symbols();
        });

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
