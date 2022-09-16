<?php

namespace Olssonm\VeryBasicAuth\Tests\Fixtures;

use Illuminate\Http\Request;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;

class CustomResponseHandler implements ResponseHandler
{
    public function __invoke(Request $request)
    {
        return response('Custom response', 401);
    }
}
