<?php

namespace Perry\Files;

class StoragePathResolver
{
    public static function resolveCacheFolder(): string
    {
        return storage_path('/swagger/cache');
    }

    public static function resolveDocumentationFolder(): string
    {
        return storage_path('/swagger/documentation');
    }

    public static function resolveRequestsFolder(): string
    {
        return storage_path('/swagger/requests');
    }
}
