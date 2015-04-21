<?php namespace Kiwina\Menu;

class ServiceProvider extends Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/settings.php' => config_path('laravel5-menu.php'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../views', 'laravel5-menu');

    // Extending Blade engine
    require_once __DIR__.'/../Extensions/BladeExtension.php';
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $app = $this->app;

        // merge default configs
        $this->mergeConfigFrom(__DIR__.'/../config/views.php', 'laravel5-menu.views');
        $this->mergeConfigFrom(__DIR__.'/../config/settings.php', 'laravel5-menu');

        $app['menu'] = $app->share(function ($app) {
            return new Menu($app['config']->get('laravel5-menu'));
        });
    }
}
