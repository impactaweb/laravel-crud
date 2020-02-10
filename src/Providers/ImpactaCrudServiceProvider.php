<?php

namespace Impactaweb\Crud\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
// use Illuminate\Support\Facades\Blade;
use Impactaweb\Crud\Commands\CrudCreate;
use Impactaweb\Crud\Commands\CrudCustomController;
use Impactaweb\Crud\Commands\CrudCustomRequest;
use Impactaweb\Crud\Commands\CrudForm;

class ImpactaCrudServiceProvider extends LaravelServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../Form/Resources/assets' => public_path('vendor/laravel-crud'),
        ], 'public');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudCreate::class,
                CrudCustomRequest::class,
                CrudCustomController::class,
                CrudForm::class
            ]);
        }
        
        // $this->loadViewsFrom(__DIR__.'/resources/views', 'laraform');
    }
}