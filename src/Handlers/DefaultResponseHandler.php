<?php

namespace Olssonm\VeryBasicAuth\Handlers;

use Illuminate\Http\Request;

class DefaultResponseHandler implements ResponseHandler
{
    public function __invoke(Request $request)
    {
        // Build header
        $header = [
            'WWW-Authenticate' => sprintf(
                'Basic realm="%s", charset="UTF-8"',
                config('very_basic_auth.realm', 'Basic Auth')
            ),
        ];

        // View
        $view = config('very_basic_auth.error_view');

        // If the request want's JSON, else view
        if ($request->wantsJson()) {
            return response()->json([
                'message' => config('very_basic_auth.error_message'),
            ], 401, $header);
        } elseif (isset($view)) {
            return response()->view($view, [], 401)
                ->withHeaders($header);
        }

        // Return default message
        return response(config('very_basic_auth.error_message'), 401, $header);
    }
}
