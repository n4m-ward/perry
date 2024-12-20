<?php

namespace Tests\Dummy;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DummyController
{
    public function dummyRequest(Request $request): JsonResponse
    {
        return response()->json([]);
    }
}