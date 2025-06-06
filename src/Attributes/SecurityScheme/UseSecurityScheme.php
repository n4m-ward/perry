<?php

namespace Perry\Attributes\SecurityScheme;

#[\Attribute]
class UseSecurityScheme
{
    public function __construct(
        public readonly string $securityScheme,
        public readonly array $scopes = [],
    ) {
    }
}
