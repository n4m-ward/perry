<?php

namespace Perry\PerryHttp;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
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
        $this->validateHttpRequestUsage();
        $response = $this->testCase->post($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
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

    private function validateHttpRequestUsage(): void
    {
        if (!in_array(MakesHttpRequests::class, class_uses($this->testCase))) {
            throw new \InvalidArgumentException('TestCase must use MakesHttpRequests');
        }
    }
}
