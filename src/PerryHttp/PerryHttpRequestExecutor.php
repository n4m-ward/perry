<?php

namespace Perry\PerryHttp;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\OpenApiDocGenerator\Cache\SaveRequestOnCache;
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

        (new SaveRequestOnCache)->execute('post', $uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execGet($uri, array $headers = []): TestResponse
    {
        $response = $this->testCase->get($uri, $headers);
        (new SaveRequestOnCache)->execute('get', $uri, [], $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execPut($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->put($uri, $data, $headers);
        (new SaveRequestOnCache)->execute('put', $uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execPatch($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->patch($uri, $data, $headers);
        (new SaveRequestOnCache)->execute('patch', $uri, $data, $headers, $response);

        return $response;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException|PerryAttributeNotFoundException
     */
    public function execDelete($uri, array $data = [], array $headers = []): TestResponse
    {
        $response = $this->testCase->delete($uri, $data, $headers);
        (new SaveRequestOnCache)->execute('delete', $uri, $data, $headers, $response);

        return $response;
    }
}
