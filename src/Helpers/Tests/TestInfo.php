<?php

namespace Perry\Helpers\Tests;

use ReflectionException;

readonly class TestInfo
{
    public function __construct(
        public string $class,
        public string $method,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function getReflectionClass(): \ReflectionClass
    {
        return new \ReflectionClass($this->class);
    }

    /**
     * @throws ReflectionException
     */
    public function getReflectionMethod(): \ReflectionMethod
    {
        $reflectionClass = $this->getReflectionClass();
        return $reflectionClass->getMethod($this->method);
    }
}
