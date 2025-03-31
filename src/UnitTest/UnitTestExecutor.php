<?php

namespace Perry\UnitTest;

class UnitTestExecutor
{
    public const EXIT_CODE_OK = 0;

    public function execute(): int
    {
        passthru('./vendor/bin/phpunit', $exitCode);

        return $exitCode;
    }
}