<?php

namespace Tests\OpenApiDocGenerator\Swagger;

use Perry\PerryHttp\PerryHttpRequest;
use Perry\OpenApiDocGenerator\OpenApi\ParseEndpointToOpenApiDocumentation;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveDocumentationAfterTests;

class ParseEndpointToOpenApiDocumentationTest extends BaseTestCase
{
    use PerryHttpRequest;
    use RemoveDocumentationAfterTests;

    private ParseEndpointToOpenApiDocumentation $parseEndpointToSwaggerDocumentation;

    public function setUp(): void
    {
        parent::setUp();
        $this->parseEndpointToSwaggerDocumentation = new ParseEndpointToOpenApiDocumentation();
    }

    public function test_shouldParseEndpointToOpenApiDocumentation(): void
    {
        $this->mockEndpointResponse('get', '/test', ['foo' => 'bar'], Response::HTTP_CREATED);
        $this->perryHttp()->get('/test');

        $parsedEndpoint = $this->parseEndpointToSwaggerDocumentation->execute('_test');

        $this->assertEquals([
            "post" => [
                "summary" => "should parse endpoint to open api documentation",
                "description" => "should parse endpoint to open api documentation",
                "operationId" => "test_shouldParseEndpointToOpenApiDocumentation",
                "responses" => [
                    201 => [
                        "description" => "201",
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "object",
                                    "properties" => [
                                        "foo" => [
                                            "type" => "string",
                                            "example" => "bar",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $parsedEndpoint);
    }
}
