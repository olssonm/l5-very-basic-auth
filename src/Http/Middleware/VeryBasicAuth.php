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
        $veryBasicAuthUser = config('very_basic_auth.user');
        $veryBasicAuthPass = config('very_basic_auth.password');
        $veryBasicAuthEnvs = config('very_basic_auth.envs');
        $veryBasicAuthMsg  = config('very_basic_auth.error_message');
        $veryBasicAuthView = config('very_basic_auth.error_view');

        // Check if middleware is in use in current environment
        if(in_array(app()->environment(), $veryBasicAuthEnvs)) {
            if($request->getUser() != $veryBasicAuthUser || $request->getPassword() != $veryBasicAuthPass) {

                $header = ['WWW-Authenticate' => 'Basic'];

                // If view is available
                if ($veryBasicAuthView) {
                    return response()->view($veryBasicAuthView, [], 401)
                        ->withHeaders($header);
                }

                // Else return default message
                return response($veryBasicAuthMsg, 401, $header);
            }
        }

        return $next($request);
    }
}
