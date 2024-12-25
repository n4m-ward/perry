<?php

namespace Perry\Files;

class StoragePathResolver
{
    public static function resolveCacheFolder(): string
    {
        return storage_path('/swagger/cache');
    }
}
