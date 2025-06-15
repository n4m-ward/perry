<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Illuminate\Testing\TestResponse;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\OpenApiDocGenerator\Cache\Dtos\TestRequestDto;

class TestRequestDtoGenerator
{
    public static function generate(
        string $method,
        string $uri,
        array $data,
        array $headers,
        TestResponse $response,
        array $usedSecurityScheme = [],
        array $usedTags = [],
        array $routeParameters = [],
    ): TestRequestDto {
        return new TestRequestDto(
            testName: TestInfoResolver::resolve()->method,
            method: $method,
            path: $uri,
            statusCode: $response->getStatusCode(),
            headers: $headers,
            query: [],
            body: $data,
            response: $response->getContent(),
            usedSecurityScheme: $usedSecurityScheme,
            usedTags: $usedTags,
            routeParameters: $routeParameters,
        );
    }
}
