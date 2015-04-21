<?php namespace Kiwina\Menu;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'laravel5-menu');

        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('laravel5-menu.php'),
            __DIR__ . '/../views' => base_path('resources/views/vendor/kiwina'),
        ]);
       
        // Extending Blade engine
        require_once __DIR__.'/Extensions/BladeExtension.php';
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // merge default configs
        $this->mergeConfigFrom(__DIR__.'/../config/views.php', 'laravel5-menu.views');
        $this->mergeConfigFrom(__DIR__.'/../config/settings.php', 'laravel5-menu');
        $this->app->register('Illuminate\Html\HtmlServiceProvider');
        $this->app->bindShared('menu', function($app) {
			return new Menu($app['config']->get('laravel5-menu'),$app['view'],$app['html'], $app['url']);
		});
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['menu'];
	}
}
