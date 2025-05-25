<?php

namespace Perry\PerryHttp;


use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

trait PerryHttpRequest
{
    use MakesHttpRequests;

    public function perryHttp(): PerryHttp
    {
        return new PerryHttp($this);
    }
}