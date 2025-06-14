<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\Helpers\Laravel\LaravelRouteFinder;

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
        $testRequestDto = TestRequestDtoGenerator::generate($method, $realUri, $data, $headers, $response, $usedSecurityScheme, $usedTags);
        Storage::saveTestRequest($testRequestDto);
    }
}
