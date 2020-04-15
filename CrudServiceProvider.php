<?php

namespace Impactaweb\Crud;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class CrudServiceProvider extends LaravelServiceProvider
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
        $this->bootForm();
        $this->bootListing();
    }

    /**
     * Form maker Boot
     */
    public function bootForm()
    {
        // Default configs
        // It can be replaced by the user in laravel /config/form.php file
        $this->mergeConfigFrom(__DIR__.'/Form/Config/form.php', 'form');

        // Form Views
        $this->loadViewsFrom(__DIR__.'/Form/Resources/views', 'form');

        // Lang
        $this->loadTranslationsFrom(__DIR__.'/Form/Resources/lang', 'form');
        $this->publishes([
            __DIR__.'/Form/Resources/lang' => resource_path('lang/vendor/form'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CrudCreate::class,
                Commands\CrudCustomController::class,
                Commands\CrudCustomRequest::class,
                Commands\CrudForm::class
            ]);
        }
        
        $this->publishes([
            __DIR__.'/Form/Resources/assets' => public_path('vendor/impactaweb/crud/form'),
        ], 'public');
    
    }

    /**
     * Listing boot
     */
    public function bootListing()
    {
        // Default configs
        // It can be replaced by the user in laravel /config/form.php file
        $this->mergeConfigFrom(__DIR__.'/Listing/Config/listing.php', 'listing');

        // Translations
        $this->loadTranslationsFrom(__DIR__.'/Listing/Resources/lang', 'listing');

        // listing Views
        $this->loadViewsFrom(__DIR__.'/Listing/Resources/views', 'listing');

        // Publish Config
        $this->publishes([
            __DIR__.'Listing/Config/listing.php' => config_path('listing.php'),
        ], 'config');

        // Publish Views
        $this->publishes([
            __DIR__.'/Listing/Resources/views' => resource_path('views/vendor/impactaweb/crud/listing'),
        ], 'views');

        $this->publishes([
            __DIR__.'/Listing/Resources/assets' => public_path('vendor/impactaweb/crud/listing'),
        ], 'public');
    }

}
