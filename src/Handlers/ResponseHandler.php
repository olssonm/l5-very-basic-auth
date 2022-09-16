<?php

namespace Olssonm\VeryBasicAuth\Handlers;

use Illuminate\Http\Request;

interface ResponseHandler
{
    public function __invoke(Request $request);
}
