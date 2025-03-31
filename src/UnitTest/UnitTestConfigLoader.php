<?php

namespace Perry\UnitTest;

use Perry\Exceptions\PerryException;
use Perry\Exceptions\PerryStorageException;
use Perry\ProjectExecutionMode;

class UnitTestConfigLoader
{
    const CONFIG_FILE_NAME = 'perry.json';

    /**
     * @throws PerryException
     */
    public static function load(): ?UnitTestConfig
    {
        $fileDecoded = self::tryLoadFile();

        if (is_null($fileDecoded)) {
            return null;
        }

        return UnitTestConfig::fromArray($fileDecoded);
    }

    /**
     * @throws PerryStorageException
     * @throws PerryException
     */
    public static function loadOrFail(): UnitTestConfig
    {
        $unitTestConfig = self::load();

        if (is_null($unitTestConfig)) {
            throw new PerryStorageException("file perry.json not found on project root");
        }

        return $unitTestConfig;
    }

    private static function tryLoadFile(): ?array
    {
        $rootFolder = ProjectExecutionMode::load()->getRootFolder();
        $perryJsonFilePath = $rootFolder . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;

        if (!file_exists($perryJsonFilePath)) {
            return null;
        }
        $perryJsonFile = file_get_contents($perryJsonFilePath);

        try {
            return json_decode($perryJsonFile, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new PerryException("File perry.json malformed");
        }
    }
}