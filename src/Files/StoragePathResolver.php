<?php

namespace Perry\Files;

use Perry\ProjectExecutionMode;
use Perry\UnitTest\UnitTestConfigLoader;

class StoragePathResolver
{
    public static function resolveCacheFolder(): string
    {
        $unitTestConfig = UnitTestConfigLoader::loadOrFail();
        return ProjectExecutionMode::load()->getRootFolder() . $unitTestConfig->cacheOutputPath;
    }

    public static function resolveDocumentationFolder(): string
    {
        $unitTestConfig = UnitTestConfigLoader::loadOrFail();
        return ProjectExecutionMode::load()->getRootFolder() . $unitTestConfig->swaggerOutputPath;
    }

    public static function resolveRequestsFolder(): string
    {
        $unitTestConfig = UnitTestConfigLoader::loadOrFail();
        return ProjectExecutionMode::load()->getRootFolder() . $unitTestConfig->cacheOutputPath . '/requests';
    }
}
