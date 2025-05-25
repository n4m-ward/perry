<?php

namespace Perry\PerryHttp;

use Illuminate\Testing\TestResponse;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use PHPUnit\Framework\TestCase;

final class PerryHttp
{
    private array $headers = [];
    private array $body = [];

    public function __construct(
        private TestCase $testCase,
    ){}

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function withBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function post(string $uri): TestResponse
    {
        return $this->getRequestExecutor()->execPost($uri, $this->body, $this->headers);
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function get(string $uri): TestResponse
    {
        return $this->getRequestExecutor()->execGet($uri, $this->headers);
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function put(string $uri): TestResponse
    {
        return $this->getRequestExecutor()->execPut($uri, $this->headers);
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function patch(string $uri): TestResponse
    {
        return $this->getRequestExecutor()->execPatch($uri, $this->headers);
    }

    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function delete(string $uri): TestResponse
    {
        return $this->getRequestExecutor()->execDelete($uri, $this->headers);
    }

    private function getRequestExecutor(): PerryHttpRequestExecutor
    {
        return new PerryHttpRequestExecutor($this->testCase);
    }
}