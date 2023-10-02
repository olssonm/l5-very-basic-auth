<?php

namespace Olssonm\VeryBasicAuth\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Olssonm\VeryBasicAuth\Handlers\DefaultResponseHandler;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;

class VeryBasicAuth
{
    protected $responseHandler;

    public function __construct(ResponseHandler $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * Handle an incoming request
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  mixed    $username
     * @param  mixed    $password
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $username = null, $password = null): Response
    {
        $active = (count(array_intersect([
            '*',
            app()->environment(),
        ], config('very_basic_auth.envs'))) > 0);

        // Check if middleware is in use in current environment
        if ($active) {
            $authUsername = $username ?? config('very_basic_auth.user');
            $authPassword = $password ?? config('very_basic_auth.password');

            if (!$authUsername && !$authPassword) {
                return $next($request);
            } elseif ($request->getUser() !== $authUsername || $request->getPassword() !== $authPassword) {
                return $this->deniedResponse($request);
            }
        }

        return $next($request);
    }

    /**
     * Return a error response
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    private function deniedResponse(Request $request): Response
    {
        return ($this->responseHandler)($request);
    }
}
