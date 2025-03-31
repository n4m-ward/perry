<?php

namespace Perry\PerryHttp;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
use Perry\SwaggerGenerator\Cache\TestRequestDtoGenerator;

trait PerryHttpRequest
{
    use MakesHttpRequests;

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function post($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = parent::post($uri, $data, $headers);
        (new GenerateSwaggerRootData())->execute();
        $testRequestDto = TestRequestDtoGenerator::generate('post', $uri, $data, $headers, $response);
        Storage::saveTestRequest($testRequestDto);

        return $response;
    }
}
