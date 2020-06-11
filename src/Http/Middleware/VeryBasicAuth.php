<?php

namespace Olssonm\VeryBasicAuth\Http\Middleware;

use \Illuminate\Http\Request;

use Closure;

class VeryBasicAuth
{
    /**
     * Handle an incoming request
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure $next
     * @param mixed $username
     * @param mixed $password
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $username = null, $password = null)
    {
        $active = (count(array_intersect([
            '*',
            app()->environment(),
        ], config('very_basic_auth.envs'))) > 0);

        // Check if middleware is in use in current environment
        if ($active) {

            $authUsername = (empty($username)) ? config('very_basic_auth.user') : $username;
            $authPassword = (empty($password)) ? config('very_basic_auth.password') : $password;

            // Check for credentials
            if ($request->getUser() !== $authUsername || $request->getPassword() !== $authPassword) {
                return $this->deniedResponse($request);
            }
        }

        return $next($request);
    }

    /**
     * Return a error response
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    private function deniedResponse(Request $request)
    {
        // Build header
        $header = [
            'WWW-Authenticate' => sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm', 'Basic Auth'))
        ];

        // View
        $view = config('very_basic_auth.error_view');

        // If the request want's JSON
        if ($request->wantsJson()) {
            return response()->json([
                'message' => config('very_basic_auth.error_message')
            ], 401, $header);
        }
        // View is available
        else if (isset($view)) {
            return response()->view($view, [], 401)
                ->withHeaders($header);
        }

        // Return default message
        return response(config('very_basic_auth.error_message'), 401, $header);
    }
}
