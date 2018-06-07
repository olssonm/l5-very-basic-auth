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
        // Check if middleware is in use in current environment
        if(count(array_intersect(['*', app()->environment()], config('very_basic_auth.envs'))) > 0) {

            // Check for credentials
            if($request->getUser() != config('very_basic_auth.user') || $request->getPassword() != config('very_basic_auth.password')) {

                // Build header
                $header = ['WWW-Authenticate' => sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm', 'Basic Auth'))];

                // If view is available
                $view = config('very_basic_auth.error_view');
                if (isset($view)) {
                    return response()->view($view, [], 401)
                        ->withHeaders($header);
                }

                // Else return default message
                return response(config('very_basic_auth.error_message'), 401, $header);
            }
        }

        return $next($request);
    }
}
