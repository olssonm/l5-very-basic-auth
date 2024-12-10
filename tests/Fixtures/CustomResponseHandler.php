<?php

namespace Olssonm\VeryBasicAuth\Tests\Fixtures;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;

class CustomResponseHandler implements ResponseHandler
{
    public function __invoke(Request $request): Response|JsonResponse
    {
        return response('Custom response', 401);
    }
}
