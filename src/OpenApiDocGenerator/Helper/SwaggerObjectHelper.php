<?php

namespace Perry\OpenApiDocGenerator\Helper;

class SwaggerObjectHelper
{
    public static function formatToSwagger(array $input): array {
        return array_map(self::formatTypeFromSingleItem(...), $input);
    }

    private static function formatTypeFromSingleItem(mixed $value): array
    {
        if (is_bool($value)) {
            return [
                'type' => 'boolean',
                'example' => $value,
            ];
        }

        if (is_int($value)) {
            return [
                'type' => 'integer',
                'format' => 'int32',
                'example' => $value,
            ];
        }

        if (is_float($value)) {
            return [
                'type' => 'number',
                'format' => 'int32',
                'example' => $value,
            ];
        }

        if (is_string($value)) {
            return [
                'type' => 'string',
                'example' => $value,
            ];
        }

        if (is_array($value)) {
            if (array_keys($value) === range(0, count($value) - 1)) {
                return [
                    'type' => 'array',
                    'items' => self::formatTypeFromSingleItem($value[0] ?? null),
                    'example' => $value,
                ];
            } else {
                return [
                    'type' => 'object',
                    'properties' => self::formatToSwagger($value),
                    'example' => $value,
                ];
            }
        }

        if (is_null($value)) {
            return [
                'type' => 'null',
                'example' => null,
            ];
        }

        return [
            'type' => 'string',
            'example' => $value,
        ];
    }
}