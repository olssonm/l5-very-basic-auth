<?php namespace Olssonm\VeryBasicAuth;

use Illuminate\Support\ServiceProvider;

class VeryBasicAuthServiceProvider extends ServiceProvider
{

    /**
     * Path to config-file
     * @var string
     */
    protected $config;

    /**
     * Path to stub
     * @var string
     */
    protected $stub;

    /**
     * Constructor
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app) {
        $this->config = __DIR__ . '/config.php';
        $this->stub = __DIR__ . '/config.stub';

        // Check that config-file exists
        if(!file_exists($this->config)) {
            $this->createConfig();
        }

        parent::__construct($app);
    }

    /**
     * Perform post-registration booting of services.
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
    }

    /**
     * Register any package services.
     * @return void
     */
    public function register()
    {
        // If the user doesn't set their own config, load default
        $this->mergeConfigFrom(
            $this->config, 'very_basic_auth'
        );
    }

    /**
     * Crates a new config-file with a random password
     * @return string bytes written
     */
    private function createConfig()
    {
        $data = file_get_contents($this->stub);
        $data = str_replace('%password%', str_random(8), $data);
        return file_put_contents($this->config, $data);
    }
}
