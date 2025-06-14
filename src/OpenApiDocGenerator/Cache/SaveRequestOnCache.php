<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;

class SaveRequestOnCache
{
    /**
     * @throws \ReflectionException
     * @throws PerryAttributeNotFoundException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execute($uri, array $data, array $headers, TestResponse $response): void
    {
        (new SaveOpenApiRootDataOnCache())->execute();
        (new SaveOpenApiSecuritySchemeOnCacheIfExists())->execute();
        (new SaveTagsOnCacheIfExists())->execute();
        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();
        $usedTags = (new FindUsedTags())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('post', $uri, $data, $headers, $response, $usedSecurityScheme, $usedTags);
        Storage::saveTestRequest($testRequestDto);
    }
}
