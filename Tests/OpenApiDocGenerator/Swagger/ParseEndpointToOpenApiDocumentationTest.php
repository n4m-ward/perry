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

        $parsedEndpoint = $this->parseEndpointToSwaggerDocumentation->execute('test');

        $this->assertEquals([
            "get" => [
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

    public function test_shouldParseEndpointToOpenApiDocumentationUsingPathParameters(): void
    {
        $this->mockEndpointResponse('get', '/api/user/{user_id}/product/{product_id}', ['foo' => 'bar'], Response::HTTP_CREATED);
        $this->perryHttp()->get('/api/user/123/product/456');

        $parsedEndpoint = $this->parseEndpointToSwaggerDocumentation->execute('api_user_{user_id}_product_{product_id}');

        $this->assertEquals([
            [
                "name" => "user_id",
                "in" => "path",
                "required" => true,
                "schema" => [
                    "type" => "string",
                ],
            ],
            [
                "name" => "product_id",
                "in" => "path",
                "required" => true,
                "schema" => [
                    "type" => "string",
                ],
            ]
        ], $parsedEndpoint['get']['parameters']);
    }

    public function test_shouldParseEndpointToOpenApiDocumentationUsingHeaderParameters(): void
    {
        $this->mockEndpointResponse('get', '/api/user', ['foo' => 'bar'], Response::HTTP_CREATED);
        $this
            ->perryHttp()
            ->withHeaders([
                'Authorization' => 'Bearer token',
            ])
            ->get('/api/user?token=some_token&user_id=123');

        $parsedEndpoint = $this->parseEndpointToSwaggerDocumentation->execute('api_user');

        $this->assertEquals([
            [
                "name" => "token",
                "in" => "query",
                "required" => false,
                "schema" => [
                    "type" => "string",
                ],
            ],
            [
                "name" => "user_id",
                "in" => "query",
                "required" => false,
                "schema" => [
                    "type" => "string",
                ],
            ],
            [
                "name" => "Authorization",
                "in" => "header",
                "required" => true,
                "schema" => [
                    "type" => "string",
                ],
            ],
        ], $parsedEndpoint['get']['parameters']);
    }
}
