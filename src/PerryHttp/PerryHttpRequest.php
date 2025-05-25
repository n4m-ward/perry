<?php

namespace Perry\PerryHttp;


trait PerryHttpRequest
{
    public function perryHttp(): PerryHttp
    {
        return new PerryHttp($this);
    }
}