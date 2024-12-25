<?php

namespace Perry\Attributes;

use Attribute;

#[\Attribute]
readonly class Info
{
    public function __construct(
        public string $version,
        public string $title,
        public string $description,
    ) {
    }
}
