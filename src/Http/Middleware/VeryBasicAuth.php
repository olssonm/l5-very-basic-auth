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

        // Check if middleware is in use in current environment
        if(in_array(app()->environment(), $veryBasicAuthEnvs)) {
            if($request->getUser() != $veryBasicAuthUser || $request->getPassword() != $veryBasicAuthPass) {
                $headers = array('WWW-Authenticate' => 'Basic');
                return response($veryBasicAuthMsg, 401, $headers);
            }
        }

        return $next($request);
    }
}
