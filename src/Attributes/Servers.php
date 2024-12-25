<?php

namespace Perry\Attributes;


#[\Attribute]
readonly class Servers
{
    /**
     * @var Server[]
     */
    public array $servers;

    public function __construct(
        Server ...$servers,
    ) {
        $this->servers = $servers;
    }
}
