<?php

namespace Tests\SwaggerGenerator\Swagger;

use Perry\PerryHttp\PerryHttpRequest;
use Perry\SwaggerGenerator\Swagger\ParseEndpointToSwaggerDocumentation;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveSwaggerAfterTests;

class ParseEndpointToSwaggerDocumentationTest extends BaseTestCase
{
    use PerryHttpRequest;
    use RemoveSwaggerAfterTests;

    private ParseEndpointToSwaggerDocumentation $parseEndpointToSwaggerDocumentation;

    public function setUp(): void
    {
        parent::setUp();
        $this->parseEndpointToSwaggerDocumentation = new ParseEndpointToSwaggerDocumentation();
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
