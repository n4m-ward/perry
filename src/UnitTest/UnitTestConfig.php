<?php

namespace Perry\UnitTest;

readonly class UnitTestConfig
{
    public function __construct(
        public string $testsFolderPath,
        public string $testExecutorPath,
        public string $swaggerOutputPath,
        public string $cacheOutputPath,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            testsFolderPath: $data['testsFolderPath'],
            testExecutorPath: $data['testExecutorPath'],
            swaggerOutputPath: $data['swaggerOutputPath'],
            cacheOutputPath: $data['cacheOutputPath'],
        );
    }
}