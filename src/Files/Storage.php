<?php

namespace Perry\Files;

use Perry\Exceptions\PerryStorageException;
use Perry\SwaggerCache\SwaggerRootInfo;

class Storage
{
    private const ROOT_INFO_DIR = 'root_info';

    public static function saveTestRootInfo(SwaggerRootInfo $rootInfo): void
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR;
        if(!is_dir($rootInfoFolder)) {
            mkdir($rootInfoFolder);
        }

        self::saveFile($rootInfoFolder . '/root_info', serialize($rootInfo));
    }

    /**
     * @throws PerryStorageException
     */
    public static function getRootInfo(): SwaggerRootInfo
    {
        $cacheFolder = self::getCacheFolder();
        $rootInfoFolder = $cacheFolder . '/'. self::ROOT_INFO_DIR . '/root_info';
        $rootInfoSerialized = file_get_contents($rootInfoFolder);

        if(empty($rootInfoSerialized)) {
            throw new PerryStorageException('Root info file not found on cache!');
        }

        return unserialize($rootInfoSerialized);
    }

    private static function saveFile(string $file, string $content): void
    {
        file_put_contents($file, $content);
    }

    private static function getCacheFolder(): string
    {
        $cacheFolder = StoragePathResolver::resolveCacheFolder();

        if(!is_dir($cacheFolder)) {
            mkdir($cacheFolder, 0777, true);
        }
        return $cacheFolder;
    }
}
