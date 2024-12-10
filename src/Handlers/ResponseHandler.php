<?php

namespace Olssonm\VeryBasicAuth\Handlers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface ResponseHandler
{
    public function __invoke(Request $request): Response|JsonResponse;
}
