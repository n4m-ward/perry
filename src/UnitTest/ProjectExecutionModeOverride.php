<?php

namespace Perry\UnitTest;

use Perry\ProjectExecutionMode;

class ProjectExecutionModeOverride
{
    private static ?ProjectExecutionMode $projectExecutionMode = null;

    public static function set(ProjectExecutionMode $projectExecutionMode): void
    {
        self::$projectExecutionMode = $projectExecutionMode;
    }

    public static function shouldOverride(): bool
    {
        return !is_null(self::$projectExecutionMode);
    }

    public static function load(): ProjectExecutionMode
    {
        if(self::$projectExecutionMode) {
            return self::$projectExecutionMode;
        }

        throw new \Exception("Execution mode not set");
    }
}