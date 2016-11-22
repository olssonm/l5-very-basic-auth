<?php namespace Olssonm\VeryBasicAuth;

use Illuminate\Support\ServiceProvider;

class VeryBasicAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        $config = __DIR__ . '/config.php';

        // Publishing of configuration
        $this->publishes([
            $config => config_path('very_basic_auth.php'),
        ]);

        // Load default view/s
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'very_basic_auth');

        // Register middleware
        $router->middleware('auth.very_basic', 'Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $config = __DIR__ . '/config.php';

        // Check that config-file exists
        if(!file_exists($config)) {
            $this->createConfig();
        }

        // If the user doesn't set their own config, load default
        $this->mergeConfigFrom(
            $config, 'very_basic_auth'
        );
    }

    /**
     * Crates a new config-file with a random password
     * @return str bytes written
     */
    private function createConfig()
    {
        $config = __DIR__ . '/config.php';
        $stub = __DIR__ . '/config.stub';

        $data = file_get_contents($stub);
        $data = str_replace('%password%', str_random(8), $data);
        return file_put_contents($config, $data);
    }
}
