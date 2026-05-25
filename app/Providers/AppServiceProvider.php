<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SubscriptionService;
use App\Services\PaymentService;
use App\Services\QuestionService;
use App\Services\TestService;
use App\Services\AnalyticsService;
use App\Services\PaymentGateways\PaymentGatewayManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register payment gateway manager as singleton
        $this->app->singleton(PaymentGatewayManager::class, function () {
            return new PaymentGatewayManager();
        });

        // Register subscription service with payment gateway manager
        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService($app->make(PaymentGatewayManager::class));
        });

        $this->app->singleton(PaymentService::class);
        $this->app->singleton(QuestionService::class);
        $this->app->singleton(TestService::class);
        $this->app->singleton(AnalyticsService::class);
    }

    public function boot(): void
    {
        // Override asset URL generation to include /public/ in the path
        if (!app()->runningInConsole()) {
            // Extend the asset() helper to serve from /public/
            $this->app['url']->macro('assetFromPublic', function ($path = null) {
                return asset('public/' . $path);
            });
        }
    }
}
