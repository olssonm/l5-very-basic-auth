<?php namespace Olssonm\VeryBasicAuth\Http\Middleware;

use Closure;

class VeryBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Load configuration
        $config = config('very_basic_auth');

        // Check if middleware is in use in current environment
        if(in_array(app()->environment(), $config['envs'])) {
            if($request->getUser() != $config['user'] || $request->getPassword() != $config['password']) {

                $header = ['WWW-Authenticate' => 'Basic'];

                // If view is available
                if (isset($config['error_view'])) {
                    return response()->view($config['error_view'], [], 401)
                        ->withHeaders($header);
                }

                // Else return default message
                return response($config['error_message'], 401, $header);
            }
        }

        return $next($request);
    }
}
