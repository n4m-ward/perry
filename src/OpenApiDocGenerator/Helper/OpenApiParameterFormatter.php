<?php

namespace Perry\OpenApiDocGenerator\Helper;

class OpenApiParameterFormatter
{
    public static function format(array $parameters, string $in, bool $required = true): array
    {
        $output = [];

        foreach ($parameters as $key => $value) {
            $output[] = [
                'name' => $key,
                'in' => $in,
                'required' => $required,
                'schema' => [
                    'type' => 'string',
                ],
            ];
        }

        return $output;
    }
}