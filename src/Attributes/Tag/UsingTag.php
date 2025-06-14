<?php

namespace Perry\Attributes\Tag;


#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UsingTag
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
