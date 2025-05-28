<?php

namespace Olssonm\VeryBasicAuth\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;
use Symfony\Component\HttpFoundation\Response;

class VeryBasicAuth
{
    protected ResponseHandler $responseHandler;

    public function __construct(ResponseHandler $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * Handle an incoming request
     *
     * @param  mixed  $username
     * @param  mixed  $password
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

            if (! $authUsername && ! $authPassword) {
                return $next($request);
            }

            $plainPassword = $request->getPassword();

            $isCorrectPassword = $plainPassword === $authPassword;

            if (! $isCorrectPassword) {
                try {
                    $isCorrectPassword = Hash::check($plainPassword, $authPassword);
                } catch (\Throwable $e) {
                    // If the password is not hashed, we will just return false
                    $isCorrectPassword = false;
                }
            }

            if ($request->getUser() !== $authUsername || !$isCorrectPassword) {
                return $this->deniedResponse($request);
            }
        }

        return $next($request);
    }

    /**
     * Return a error response
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    private function deniedResponse(Request $request): Response|JsonResponse
    {
        return ($this->responseHandler)($request);
    }
}
