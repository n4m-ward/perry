<?php

namespace Perry\OpenApiDocGenerator\Helper;

class UrlParser
{
    public static function parseQueryParamsFromUrl(string $url): array
    {
        $parsedUrl = parse_url($url);
        if(!array_key_exists('query', $parsedUrl)) {
            return [];
        }
        parse_str($parsedUrl['query'], $output);
        return $output;
    }
}
