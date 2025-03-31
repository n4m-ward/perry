<?php

namespace Perry\ProjectSetup;

use Perry\Exceptions\PerryException;
use Perry\ProjectExecutionMode;
use Perry\UnitTest\UnitTestConfigLoader;

class ProjectSetup
{
    /**
     * @throws PerryException
     */
    public function executeAndExitIfNecessary(): void
    {
        if ($this->alreadyHaveSetup()) {
            return;
        }

        echo "✅ Generating default project config file \n\n";

        $defaultUnitTestConfig = <<<JSON
{
    "testsFolderPath": "/tests/Perry",
    "testExecutorPath": "/vendor/bin/phpunit",
    "swaggerOutputPath": "/perry_output/swagger",
    "cacheOutputPath": "/perry_output/cache"
}
JSON;

        $executionMode = ProjectExecutionMode::load();
        $projectRootFolder = $executionMode->getRootFolder();

        file_put_contents($projectRootFolder. '/perry.json', $defaultUnitTestConfig);

        $perryOutputFolder = $projectRootFolder . '/perry_output';
        if (!is_dir($perryOutputFolder)) {
            mkdir($perryOutputFolder);
        }

        exit(0);
    }

    /**
     * @throws PerryException
     */
    private function alreadyHaveSetup(): bool
    {
        $unitTestConfig = UnitTestConfigLoader::load();

        return !is_null($unitTestConfig);
    }
}