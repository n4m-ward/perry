<?php

namespace Perry\SwaggerGenerator\Helper;

class SwaggerObjectHelper
{
    public static function formatToSwagger(array $input): array {
        $output = [];

        foreach ($input as $key => $value) {
            if (is_bool($value)) {
                $output[$key] = [
                    'type' => 'boolean',
                    'example' => $value,
                ];
            } elseif (is_int($value)) {
                $output[$key] = [
                    'type' => 'integer',
                    'format' => 'int32',
                    'example' => $value,
                ];
            } elseif (is_float($value)) {
                $output[$key] = [
                    'type' => 'number',
                    'format' => 'int32',
                    'example' => $value,
                ];
            } elseif (is_string($value)) {
                $output[$key] = [
                    'type' => 'string',
                    'example' => $value,
                ];
            } elseif (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    $output[$key] = [
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                        ],
                        'example' => $value,
                    ];
                } else {
                    $output[$key] = [
                        'type' => 'object',
                        'properties' => self::formatToSwagger($value),
                        'example' => $value,
                    ];
                }
            } elseif (is_null($value)) {
                $output[$key] = [
                    'type' => 'null',
                    'example' => null,
                ];
            } else {
                $output[$key] = [
                    'type' => 'string',
                    'example' => $value,
                ];
            }
        }


        return $output;
    }
}