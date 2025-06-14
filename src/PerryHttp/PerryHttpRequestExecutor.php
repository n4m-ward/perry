<?php

namespace Perry\PerryHttp;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\Cache\FindUsedSecurityScheme;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
use Perry\SwaggerGenerator\Cache\SaveRequestOnCache;
use Perry\SwaggerGenerator\Cache\SaveSwaggerSecuritySchemeIfExists;
use Perry\SwaggerGenerator\Cache\SaveTagsIfExists;
use Perry\SwaggerGenerator\Cache\TestRequestDtoGenerator;
use PHPUnit\Framework\TestCase;

readonly class PerryHttpRequestExecutor
{
    /**
     * @param TestCase&MakesHttpRequests $testCase
     */
    public function __construct(
        private TestCase $testCase,
    ) {
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execPost($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->post($uri, $data, $headers);

        (new SaveRequestOnCache)->execute($uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execGet($uri, array $headers = []): TestResponse
    {
        $response = $this->testCase->get($uri, $headers);
        (new SaveRequestOnCache)->execute($uri, [], $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execPut($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->put($uri, $data, $headers);
        (new SaveRequestOnCache)->execute($uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execPatch($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->patch($uri, $data, $headers);
        (new SaveRequestOnCache)->execute($uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execDelete($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->delete($uri, $data, $headers);
        (new SaveRequestOnCache)->execute($uri, $data, $headers, $response);

        return $response;
    }
}
