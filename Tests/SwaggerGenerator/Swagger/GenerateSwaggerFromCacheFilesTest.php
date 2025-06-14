<?php

namespace Tests\SwaggerGenerator\Swagger;

use Perry\Files\Storage;
use Perry\PerryHttp\PerryHttpRequest;
use Perry\SwaggerGenerator\Swagger\GenerateSwaggerFromCacheFiles;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveSwaggerAfterTests;
use Tests\TestHelpers\OpenApiDocPayload;

class GenerateSwaggerFromCacheFilesTest extends BaseTestCase
{
    use PerryHttpRequest;
    use RemoveSwaggerAfterTests;

    private GenerateSwaggerFromCacheFiles $generateSwaggerFromCacheFiles;

    public function setUp(): void
    {
        parent::setUp();
        $this->generateSwaggerFromCacheFiles = new GenerateSwaggerFromCacheFiles();
    }

    public function test_shouldGenerateSwagger(): void
    {
        $expectedDocumentation = OpenApiDocPayload::withDefaultBody(<<<YAML
paths:
  /test:
    post:
      summary: should generate swagger
      description: should generate swagger
      operationId: test_shouldGenerateSwagger
      responses:
        201:
          description: "201"
          content:
            application/json:
              schema:
                type: object
                properties:
                  foo:
                    type: string
                    example: bar
YAML);

        $this->mockEndpointResponse('get', '/test', ['foo' => 'bar'], Response::HTTP_CREATED);
        $this->perryHttp()->get('/test');

        $this->generateSwaggerFromCacheFiles->execute();
        $doc = Storage::getSwaggerDoc();

        $this->assertEquals($expectedDocumentation, $doc);
    }
}
