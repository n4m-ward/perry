<?php

namespace Perry\Attributes\Tag;

use Perry\Attributes\ExternalDocs;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Tag
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly ?ExternalDocs $externalDocs = null,
    ) {
    }
}
