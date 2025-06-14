<?php

namespace Perry\OpenApiCache;

use Perry\Attributes\Info;
use Perry\Attributes\Servers;

readonly class OpenApiRootInfo
{
    public function __construct(
        public Info     $info,
        public ?Servers $servers = null,
    ) {
    }
}
