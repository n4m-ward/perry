<?php

namespace Perry\PerryHttp;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
use Perry\SwaggerGenerator\Cache\SaveSwaggerSecuritySchemeIfExists;
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
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execPost($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->post($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
        (new SaveSwaggerSecuritySchemeIfExists())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('post', $uri, $data, $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execGet($uri, array $headers = []): TestResponse
    {
        $response = $this->testCase->get($uri, $headers);
        (new GenerateSwaggerRootData())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('get', $uri, [], $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execPut($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->put($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('put', $uri, [], $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execPatch($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->patch($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('patch', $uri, [], $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execDelete($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->delete($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('delete', $uri, [], $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }
}
