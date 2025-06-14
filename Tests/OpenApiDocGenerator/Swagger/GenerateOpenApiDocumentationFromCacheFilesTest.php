<?php

namespace Tests\OpenApiDocGenerator\Swagger;

use Perry\Files\Storage;
use Perry\PerryHttp\PerryHttpRequest;
use Perry\OpenApiDocGenerator\OpenApi\GenerateOpenApiDocumentationFromCacheFiles;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveDocumentationAfterTests;
use Tests\TestHelpers\OpenApiDocPayload;

class GenerateOpenApiDocumentationFromCacheFilesTest extends BaseTestCase
{
    use PerryHttpRequest;
    use RemoveDocumentationAfterTests;

    private GenerateOpenApiDocumentationFromCacheFiles $generateSwaggerFromCacheFiles;

    public function setUp(): void
    {
        parent::setUp();
        $this->generateSwaggerFromCacheFiles = new GenerateOpenApiDocumentationFromCacheFiles();
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
        $doc = Storage::getOpenApiDocumentation();

        $this->assertEquals($expectedDocumentation, $doc);
    }
}
