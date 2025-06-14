<?php

namespace Perry\SwaggerGenerator\Cache;

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
        (new GenerateSwaggerRootData())->execute();
        (new SaveSwaggerSecuritySchemeIfExists())->execute();
        (new SaveTagsIfExists())->execute();
        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('post', $uri, $data, $headers, $response, $usedSecurityScheme);
        Storage::saveTestRequest($testRequestDto);
    }
}