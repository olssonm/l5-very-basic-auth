<?php

namespace Olssonm\VeryBasicAuth;

use Illuminate\Support\ServiceProvider;
use Olssonm\VeryBasicAuth\Console\PasswordGenerateCommand;
use Olssonm\VeryBasicAuth\Handlers\DefaultResponseHandler;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;

class VeryBasicAuthServiceProvider extends ServiceProvider
{
    /**
     * Path to config-file
     *
     * @var string
     */
    protected $config;

    /**
     * Path to stub
     *
     * @var string
     */
    protected $stub;

    /**
     * Constructor
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->config = __DIR__ . '/config.php';

        parent::__construct($app);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        // Publishing of configuration
        $this->publishes([
            $this->config => config_path('very_basic_auth.php'),
        ]);

        // Load default view/s
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'very_basic_auth');

        // Register middleware
        $router->aliasMiddleware('auth.very_basic', \Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PasswordGenerateCommand::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // If the user doesn't set their own config, load default
        $this->mergeConfigFrom(
            $this->config,
            'very_basic_auth'
        );

        $this->app->bind(
            ResponseHandler::class,
            config('very_basic_auth.response_handler', DefaultResponseHandler::class)
        );
    }
}
