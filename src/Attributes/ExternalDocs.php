<?php

namespace Perry\Attributes;

class ExternalDocs
{
    public function __construct(
        public string $url,
        public string $description,
    ) {
    }
}
