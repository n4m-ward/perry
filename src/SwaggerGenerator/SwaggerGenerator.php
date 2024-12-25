<?php

namespace Perry\SwaggerGenerator;

use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\SwaggerGenerator\SwaggerRoot\GenerateSwaggerRootData;
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
}
