<?php

namespace Perry;

use Perry\UnitTest\ProjectExecutionModeOverride;

enum ProjectExecutionMode
{
    case PACKAGE_MODE;
    case PROJECT_MODE;
    case UNIT_TEST;
    case PROJECT_UNIT_TEST;

    public function getRootFolder(): string
    {
        return match ($this) {
            self::PROJECT_UNIT_TEST, self::PROJECT_MODE => __DIR__ . '/..',
            self::PACKAGE_MODE, self::UNIT_TEST => __DIR__ . '/../../../..',
        };
    }

    public static function load(): self
    {
        if (ProjectExecutionModeOverride::shouldOverride()) {
            return ProjectExecutionModeOverride::load();
        }

        if(self::isProjectUsingPhpUnit()) {
            return self::UNIT_TEST;
        }

        if(self::isProjectInsideVendor()) {
            return self::PACKAGE_MODE;
        }

        return self::PROJECT_MODE;
    }

    private static function isProjectUsingPhpUnit(): bool
    {
        $scriptPath = realpath($_SERVER['argv'][0]);
        return str_contains($scriptPath, 'vendor/bin/phpunit');
    }

    private static function isProjectInsideVendor(): bool
    {
        $scriptPath = realpath($_SERVER['argv'][0]);
        return str_contains($scriptPath, 'vendor');
    }
}