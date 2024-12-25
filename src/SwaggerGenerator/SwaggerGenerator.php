<?php

namespace Perry\SwaggerGenerator;

use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Exceptions\PerryStorageException;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
use Perry\SwaggerGenerator\Swagger\GenerateSwaggerFromCacheFiles;
use Symfony\Component\HttpFoundation\Response;

class SwaggerGenerator
{
    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function generateDocAndSaveOnCache(array $parameters, Response $response): void
    {
        (new GenerateSwaggerRootData())->execute();
    }

    /**
     * @throws PerryStorageException
     */
    public function generateSwaggerFromCacheFiles(): void
    {
        (new GenerateSwaggerFromCacheFiles())->execute();
    }
}
