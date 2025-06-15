<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\Helpers\Laravel\LaravelRouteFinder;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\OpenApiDocGenerator\Cache\Dtos\TestRequestDto;
use Perry\OpenApiDocGenerator\Helper\UrlParser;

class SaveRequestOnCache
{
    /**
     * @throws \ReflectionException
     * @throws PerryAttributeNotFoundException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execute(string $method, $uri, array $data, array $headers, TestResponse $response): void
    {
        (new SaveOpenApiRootDataOnCache())->execute();
        (new SaveOpenApiSecuritySchemeOnCacheIfExists())->execute();
        (new SaveTagsOnCacheIfExists())->execute();
        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();
        $usedTags = (new FindTagsUsedByTestCase())->execute();
        $realUri = LaravelRouteFinder::findRealRoute($method, $uri);
        $pathParameters = LaravelRouteFinder::findPathParameters($method, $uri);
        $testRequestDto = new TestRequestDto(
            testName: TestInfoResolver::resolve()->method,
            method: $method,
            path: $realUri,
            statusCode: $response->getStatusCode(),
            headers: $headers,
            query: UrlParser::parseQueryParamsFromUrl($uri),
            body: $data,
            response: $response->getContent(),
            usedSecurityScheme: $usedSecurityScheme,
            usedTags: $usedTags,
            routeParameters: $pathParameters,
        );
        Storage::saveTestRequest($testRequestDto);
    }
}
