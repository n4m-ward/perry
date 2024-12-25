<?php

namespace Perry\Helpers\Tests;

use PHPUnit\Framework\TestCase;

class TestInfoResolver
{
    public static function resolve(): ?object
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($backtrace as $trace) {
            if (!isset($trace['file']) || !isset($trace['class'])) {
                continue;
            }
            if(!self::classHasSuffixTest($trace['class'])) {
                continue;
            }
            if (!self::classImplementsTestCase($trace['class'])) {
                continue;
            }

            return new TestInfo(
                class: $trace['class'],
                method: $trace['function'],
            );
        }

        return null;
    }

    private static function classHasSuffixTest(string $class): bool
    {
        return str_contains($class, 'test') || str_contains($class, 'Test');
    }

    private static function classImplementsTestCase(string $class): bool
    {
        return is_subclass_of($class, TestCase::class);
    }
}
