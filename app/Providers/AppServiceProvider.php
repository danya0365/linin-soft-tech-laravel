<?php

namespace App\Providers;

use App\Contracts\LlmProvider;
use App\Services\Llm\LlmProviderManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LlmProviderManager::class);

        // resolve LlmProvider ตรงๆ = provider เริ่มต้น (path ที่ไม่ผูกกับ model ที่ user เลือก)
        $this->app->bind(
            LlmProvider::class,
            fn ($app) => $app->make(LlmProviderManager::class)->default()
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setDefaultPaginationView();
    }

    /**
     *
     * @return void
     */
    private function setDefaultPaginationView()
    {
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}
