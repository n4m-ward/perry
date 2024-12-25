<?php

namespace Perry\Attributes;

readonly class Server
{
    public function __construct(
        public string $description,
        public string $url,
    ) {
    }
}
