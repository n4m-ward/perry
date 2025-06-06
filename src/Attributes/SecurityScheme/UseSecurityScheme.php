<?php

namespace Perry\Attributes\SecurityScheme;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class UseSecurityScheme
{
    public function __construct(
        public readonly string $securityScheme,
        public readonly array $scopes = [],
    ) {
    }
}
