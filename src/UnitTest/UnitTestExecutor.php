<?php

namespace Perry\UnitTest;

class UnitTestExecutor
{
    public const EXIT_CODE_OK = 0;

    public function execute(): int
    {
        $unitTestConfig = UnitTestConfigLoader::loadOrFail();

        passthru("./vendor/bin/phpunit {$unitTestConfig->testsFolderPath}", $exitCode);

        return $exitCode;
    }
}