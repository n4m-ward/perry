<?php

namespace Perry\SwaggerGenerator\Cache\Dtos;

use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Attributes\Tag\UsingTag;

class TestRequestDto
{
    /**
     * @param UseSecurityScheme[] $usedSecurityScheme
     * @param UsingTag[] $usedSecurityScheme
     */
    public function __construct(
        public readonly string      $testName,
        public readonly string      $method,
        public readonly string      $path,
        public readonly int         $statusCode,
        public readonly array       $headers,
        public readonly array       $query,
        public readonly array       $body,
        public readonly string|null $response,
        public readonly array       $usedSecurityScheme = [],
        public readonly array       $usedTags = [],
    ) {
    }
}
