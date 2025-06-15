<?php

namespace Perry\OpenApiDocGenerator\Helper;

class OpenApiParameterFormatter
{
    public static function format(array $parameters, string $in): array
    {
        $output = [];

        foreach ($parameters as $key => $value) {
            $output[] = [
                'name' => $key,
                'in' => $in,
                'required' => true,
                'schema' => [
                    'type' => 'string',
                ],
            ];
        }

        return $output;
    }
}