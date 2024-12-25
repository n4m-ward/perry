<?php

namespace Perry\SwaggerGenerator\Cache\Dtos;

class TestRequestDto
{
    public function __construct(
        public readonly string $testName,
        public readonly string $method,
        public readonly string $path,
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly array $query,
        public readonly array $body,
        public readonly string|null $response,
    ) {
    }
}