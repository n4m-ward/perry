<?php

namespace Perry\Attributes\SecurityScheme;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SecurityScheme
{
    public function __construct(
        public string $securityScheme,
        public string $type,
        public ?string $in = null,
        public ?string $name = null,
        public ?string $scheme = null,
    ) {
    }
}
