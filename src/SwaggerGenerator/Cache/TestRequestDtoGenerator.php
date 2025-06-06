<?php

namespace Perry\SwaggerGenerator\Cache;

use Illuminate\Testing\TestResponse;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\SwaggerGenerator\Cache\Dtos\TestRequestDto;

class TestRequestDtoGenerator
{
    public static function generate(
        string $method,
        string $uri,
        array $data,
        array $headers,
        TestResponse $response,
        array $usedSecurityScheme = [],
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
            usedSecurityScheme: $usedSecurityScheme
        );
    }
}
