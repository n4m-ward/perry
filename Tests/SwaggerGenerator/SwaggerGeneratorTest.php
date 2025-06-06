<?php

namespace Tests\SwaggerGenerator;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Tests\Base\RemoveSwaggerAfterTests;
use Illuminate\Http\Request;
use Perry\Exceptions\PerryException;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Exceptions\PerryStorageException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\SwaggerGenerator;
use ReflectionException;
use Tests\Base\BaseTestCase;

#[SecurityScheme(securityScheme: 'BearerToken', type: 'http', in: 'header', name: 'Authorization', scheme: 'bearer')]
class SwaggerGeneratorTest extends BaseTestCase
{
    use RemoveSwaggerAfterTests;

    private SwaggerGenerator $swaggerGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->swaggerGenerator = new SwaggerGenerator();
    }

    /**
     * @throws PerryStorageException
     * @throws PerryInfoAttributeNotFoundException
     * @throws ReflectionException
     * @throws PerryException
     */
    public function test_shouldGenerateRootApiDocumentation(): void
    {
        $request = new Request(
            query: $requestQuery = [
                'param1' => 'value1',
                'param2' => 'value2',
            ],
            request: $requestBody = [
                'param3' => false,
                'param4' => 4,
                'param5' => [
                    'param6' => 'value6',
                    'param7' => ['param8', 'param9'],
                ],
            ],
            server: [
                'REQUEST_URI' => '/api/expected/endpoint'
            ]
        );
        $request->setMethod('POST');
        $response = response()->json($responseArray = [
            'responseParam1' => true,
            'responseParam2' => 123,
            'responseParam3' => [
                'param4' => 'value4',
                'param5' => ['param8', 'param9'],
            ],
        ], 200);

        $this->swaggerGenerator->generateDocAndSaveOnCache([$request], $response);
        $rootInfo = Storage::getRootInfo();
        $requestDto = Storage::getSingleTestRequest('api/expected/endpoint', 'POST', 200);

        $this->assertEquals('1.0.0', $rootInfo->info->version);
        $this->assertEquals('Example server title', $rootInfo->info->title);
        $this->assertEquals('Example server description', $rootInfo->info->description);

        $this->assertEquals('Server 1', $rootInfo->servers->servers[0]->description);
        $this->assertEquals('Server 2', $rootInfo->servers->servers[1]->description);
        $this->assertEquals('https://server1.com', $rootInfo->servers->servers[0]->url);
        $this->assertEquals('https://server2.com', $rootInfo->servers->servers[1]->url);

        $this->assertEquals('test_shouldGenerateRootApiDocumentation', $requestDto->testName);
        $this->assertEquals('POST', $requestDto->method);
        $this->assertEquals('api/expected/endpoint', $requestDto->path);
        $this->assertEquals(200, $requestDto->statusCode);
        $this->assertEquals($requestQuery, $requestDto->query);
        $this->assertEquals($requestBody, $requestDto->body);
        $this->assertEquals(json_encode($responseArray), $requestDto->response);

    }


    /**
     * @throws PerryStorageException
     * @throws PerryInfoAttributeNotFoundException
     * @throws ReflectionException
     * @throws PerryException
     */
    #[UseSecurityScheme('Bearer')]
    public function test_generateDocAndSaveOnCache_shouldThrowExceptionWhenTestAreUsingSecurityScheme_ButBaseClassDoesNotHaveSecuritySchemeAttribute(): void
    {
        $this->expectException(PerryAttributeNotFoundException::class);
        $this->expectExceptionMessage('SecurityScheme [Bearer] was not implemented');

        $request = new Request(
            server: [
                'REQUEST_URI' => '/api/expected/endpoint'
            ]
        );
        $request->setMethod('POST');
        $response = response()->json();

        $this->swaggerGenerator->generateDocAndSaveOnCache([$request], $response);
    }


    /**
     * @throws PerryStorageException
     * @throws PerryInfoAttributeNotFoundException
     * @throws ReflectionException
     * @throws PerryException
     */
    #[UseSecurityScheme('BearerToken')]
    public function test_generateDocAndSaveOnCache_shouldGenerateDocWithSecurityScheme(): void
    {
        $request = new Request(
            server: [
                'REQUEST_URI' => '/api/expected/endpoint'
            ]
        );
        $request->setMethod('POST');
        $response = response()->json();

        $this->swaggerGenerator->generateDocAndSaveOnCache([$request], $response);
        $requestDto = Storage::getSingleTestRequest('api/expected/endpoint', 'POST', 200);

        $this->assertEquals('BearerToken', $requestDto->usedSecurityScheme[0]->securityScheme);
        $this->assertEquals([], $requestDto->usedSecurityScheme[0]->scopes);
    }

    /**
     * @throws ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     * @throws PerryStorageException
     */
    public function test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfo(): void
    {
        $expectedDocumentation = <<<YAML
openapi: 3.0.0
servers:
    - { description: 'Server 1', url: 'https://server1.com' }
    - { description: 'Server 2', url: 'https://server2.com' }
info:
    version: 1.0.0
    title: 'Example server title'
    description: 'Example server description'
paths:
    /: { get: { summary: 'generate swagger from cache files should generate a yaml with root info', description: 'generate swagger from cache files should generate a yaml with root info', operationId: test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfo, responses: { 200: { description: '200', content: { application/json: { schema: {  } } } } } } }
components:
    securitySchemes: { BearerToken: { type: http, in: header, name: Authorization, scheme: bearer } }

YAML;

        $this->swaggerGenerator->generateDocAndSaveOnCache([new Request()], response()->json()); // to generate the basic route info
        $this->swaggerGenerator->generateSwaggerFromCacheFiles();

        $documentation = Storage::getSwaggerDoc();

        $this->assertEquals($expectedDocumentation, $documentation);
    }

    /**
     * @throws ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     * @throws PerryStorageException
     */
    public function test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfoAndSecurityScheme(): void
    {
        $expectedDocumentation = <<<YAML
openapi: 3.0.0
servers:
    - { description: 'Server 1', url: 'https://server1.com' }
    - { description: 'Server 2', url: 'https://server2.com' }
info:
    version: 1.0.0
    title: 'Example server title'
    description: 'Example server description'
paths:
    /: { get: { summary: 'generate swagger from cache files should generate a yaml with root info and security scheme', description: 'generate swagger from cache files should generate a yaml with root info and security scheme', operationId: test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfoAndSecurityScheme, responses: { 200: { description: '200', content: { application/json: { schema: {  } } } } } } }
components:
    securitySchemes: { BearerToken: { type: http, in: header, name: Authorization, scheme: bearer } }

YAML;

        Storage::saveSecuritySchemes([new SecurityScheme(securityScheme: 'BearerToken', type: 'http', in: 'header', name: 'Authorization', scheme: 'bearer')]);

        $this->swaggerGenerator->generateDocAndSaveOnCache([new Request()], response()->json());
        $this->swaggerGenerator->generateSwaggerFromCacheFiles();

        $documentation = Storage::getSwaggerDoc();

        $this->assertEquals($expectedDocumentation, $documentation);
    }

    #[UseSecurityScheme('BearerToken')]
    public function test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfoAndUsedSecurityScheme(): void
    {
        $expectedDocumentation = <<<YAML
openapi: 3.0.0
servers:
    - { description: 'Server 1', url: 'https://server1.com' }
    - { description: 'Server 2', url: 'https://server2.com' }
info:
    version: 1.0.0
    title: 'Example server title'
    description: 'Example server description'
paths:
    /: { get: { summary: 'generate swagger from cache files should generate a yaml with root info and used security scheme', description: 'generate swagger from cache files should generate a yaml with root info and used security scheme', operationId: test_generateSwaggerFromCacheFiles_shouldGenerateAYamlWithRootInfoAndUsedSecurityScheme, responses: { 200: { description: '200', content: { application/json: { schema: {  } } } } }, security: [{ BearerToken: {  } }] } }
components:
    securitySchemes: { BearerToken: { type: http, in: header, name: Authorization, scheme: bearer } }

YAML;

        $this->swaggerGenerator->generateDocAndSaveOnCache([new Request()], response()->json());
        $this->swaggerGenerator->generateSwaggerFromCacheFiles();

        $documentation = Storage::getSwaggerDoc();

        $this->assertEquals($expectedDocumentation, $documentation);
    }
}
