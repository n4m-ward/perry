<?php

namespace Perry\SwaggerGenerator;

use Illuminate\Http\Request;
use Perry\Exceptions\PerryException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Exceptions\PerryStorageException;
use Perry\Files\Storage;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\SwaggerGenerator\Cache\Dtos\TestRequestDto;
use Perry\SwaggerGenerator\Cache\FindUsedSecurityScheme;
use Perry\SwaggerGenerator\Cache\GenerateSwaggerRootData;
use Perry\SwaggerGenerator\Cache\SaveSwaggerSecuritySchemeIfExists;
use Perry\SwaggerGenerator\Swagger\GenerateSwaggerFromCacheFiles;
use Symfony\Component\HttpFoundation\Response;

class SwaggerGenerator
{
    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     * @throws PerryException
     */
    public function generateDocAndSaveOnCache(array $parameters, Response $response): void
    {
        $request = $this->findRequestOnParameters($parameters);

        (new GenerateSwaggerRootData())->execute();
        (new SaveSwaggerSecuritySchemeIfExists())->execute();

        $usedSecurityScheme = (new FindUsedSecurityScheme())->execute();
        $dto = new TestRequestDto(
            testName: TestInfoResolver::resolve()->method,
            method: $request->getMethod(),
            path: $request->path(),
            statusCode: $response->getStatusCode(),
            headers: $request->headers->all(),
            query: $request->query->all(),
            body: $request->request->all(),
            response: $response->getContent(),
            usedSecurityScheme: $usedSecurityScheme
        );

        Storage::saveTestRequest($dto);
    }

    /**
     * @throws PerryException
     */
    private function findRequestOnParameters(array $parameters): Request
    {
        foreach ($parameters as $parameter) {
            if($parameter instanceof Request) {
                return $parameter;
            }
        }

        throw new PerryException('Request not found');
    }

    /**
     * @throws PerryStorageException
     */
    public function generateSwaggerFromCacheFiles(): void
    {
        (new GenerateSwaggerFromCacheFiles())->execute();
    }
}
