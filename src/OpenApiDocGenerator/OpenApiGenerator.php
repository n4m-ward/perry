<?php

namespace Perry\OpenApiDocGenerator;

use Illuminate\Http\Request;
use Perry\Exceptions\PerryException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Exceptions\PerryStorageException;
use Perry\Files\Storage;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\OpenApiDocGenerator\Cache\Dtos\TestRequestDto;
use Perry\OpenApiDocGenerator\Cache\FindUsedSecurityScheme;
use Perry\OpenApiDocGenerator\Cache\FindUsedTags;
use Perry\OpenApiDocGenerator\Cache\SaveOpenApiRootDataOnCache;
use Perry\OpenApiDocGenerator\Cache\SaveOpenApiSecuritySchemeOnCacheIfExists;
use Perry\OpenApiDocGenerator\Cache\SaveTagsOnCacheIfExists;
use Perry\OpenApiDocGenerator\OpenApi\GenerateOpenApiDocumentationFromCacheFiles;
use Symfony\Component\HttpFoundation\Response;

class OpenApiGenerator
{
    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     * @throws PerryException
     */
    public function generateDocAndSaveOnCache(array $parameters, Response $response): void
    {
        $request = $this->findRequestOnParameters($parameters);

        (new SaveOpenApiRootDataOnCache())->execute();
        (new SaveOpenApiSecuritySchemeOnCacheIfExists())->execute();
        (new SaveTagsOnCacheIfExists())->execute();
        $usedTags = (new FindUsedTags())->execute();

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
            usedSecurityScheme: $usedSecurityScheme,
            usedTags: $usedTags,
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
    public function GenerateOpenApiDocumentationFromCacheFiles(): void
    {
        (new GenerateOpenApiDocumentationFromCacheFiles())->execute();
    }
}
