<?php

namespace Perry\OpenApiDocGenerator\OpenApi;

use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Files\Storage;
use Perry\OpenApiDocGenerator\Cache\Dtos\TestRequestDto;
use Perry\OpenApiDocGenerator\Helper\OpenApiParameterFormatter;
use Perry\OpenApiDocGenerator\Helper\SwaggerObjectHelper;

class ParseEndpointToOpenApiDocumentation
{
    public function execute(string $endpoint): array
    {
        $output = [];
        $requestMethods = Storage::loadRequestFolder("/$endpoint");

        foreach ($requestMethods as $requestMethod) {
            $output[strtolower($requestMethod)] = $this->formatRequestMethod($endpoint, $requestMethod);
        }
        return $output;
    }

    private function formatRequestMethod(string $endpoint, string $method): array
    {
        $requestList = $this->getRequestList($endpoint, $method);
        $requestWithLowestStatusCode = $this->findRequestWithLowestStatusCode($requestList);
        $requestDescription = $this->formatTestNameToRequestDescription($requestWithLowestStatusCode->testName);
        $output = [
            'summary' => $requestDescription,
            'description' => $requestDescription,
            'operationId' => $requestWithLowestStatusCode->testName,
        ];

        if(!empty($requestWithLowestStatusCode->routeParameters)) {
            $output['parameters'] = OpenApiParameterFormatter::format($requestWithLowestStatusCode->routeParameters, in: 'path');
        }

        if(!empty($requestWithLowestStatusCode->query)) {
            $parametersFromQuery = OpenApiParameterFormatter::format($requestWithLowestStatusCode->query, in: 'query', required: false);
            $output['parameters'] = array_merge($output['parameters'] ?? [], $parametersFromQuery);
        }

        if(!empty($requestWithLowestStatusCode->headers)) {
            $parametersFromHeader =  OpenApiParameterFormatter::format($requestWithLowestStatusCode->headers, in: 'header');
            $output['parameters'] = array_merge($output['parameters'] ?? [], $parametersFromHeader);
        }

        $output['responses'] = $this->formatSwaggerResponses($requestList);

        if($requestWithLowestStatusCode->usedSecurityScheme) {
            $output['security'] = $this->formatSecurity($requestWithLowestStatusCode->usedSecurityScheme);
        }

        $tags = $this->getTagsFromRequest($requestList);
        if(!empty($tags)) {
            $output['tags'] = $tags;
        }

        if(!empty($requestWithLowestStatusCode->body)) {
            $output['requestBody'] = [
                'description' => $requestDescription,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => SwaggerObjectHelper::formatToSwagger($requestWithLowestStatusCode->body)
                        ]
                    ]
                ],
            ];
        }

        return $output;
    }

    /**
     * @param UseSecurityScheme[] $security
     */
    private function formatSecurity(array $security): array
    {
        $output = [];
        foreach ($security as $useSecurityScheme) {
            $output[][$useSecurityScheme->securityScheme] = $useSecurityScheme->scopes;
        }
        return $output;
    }

    /**
     * @param TestRequestDto[] $responses
     */
    private function formatSwaggerResponses(array $responses): array
    {
        $output = [];
        foreach ($responses as $response) {
            $responseArray = json_decode($response->response, true);
            if(is_array($responseArray)) {
                $output[(string) $response->statusCode] = [
                    'description' => (string) $response->statusCode,
                    'content' => [
                        'application/json' => [
                            'schema' => $this->addPropertiesToResponses($responseArray)
                        ]
                    ]
                ];
                continue;
            }
            $output[(string) $response->statusCode] = $response->response;
        }

        return $output;
    }

    /**
     * @param TestRequestDto[] $responses
     */
    private function getTagsFromRequest(array $responses): array
    {
        $output = [];
        foreach ($responses as $response) {
            foreach ($response->usedTags as $tag) {
                if(in_array($tag->name, $output)) {
                    continue;
                }
                $output[] = $tag->name;
            }
        }
        return $output;
    }

    private function addPropertiesToResponses(array $response): array
    {
        if(empty($response)) {
            return [];
        }

        return [
            'type' => 'object',
            'properties' => SwaggerObjectHelper::formatToSwagger($response)
        ];
    }

    private function formatTestNameToRequestDescription(string $testName): string
    {
        $testName = str_replace("test", "", $testName);
        $testName = str_replace("_", " ", $testName);
        $testName = preg_replace('/(?<!^)[A-Z]/', ' $0', $testName);

        return strtolower(trim($testName));
    }

    /**
     * @return TestRequestDto[]
     */
    private function getRequestList(string $endpoint, string $method): array
    {
        $output = [];
        $requestFolder = Storage::loadRequestFolder("/$endpoint/$method");
        foreach ($requestFolder as $request) {
            $output[(string) $request] = Storage::getSingleTestRequest($endpoint, $method, $request);
        }

        return $output;
    }

    /**
     * @param TestRequestDto[] $requestList
     */
    private function findRequestWithLowestStatusCode(array $requestList): TestRequestDto
    {
        /** @var TestRequestDto|null $requestWithLessStatusCode */
        $requestWithLessStatusCode = null;
        foreach ($requestList as $request) {
            if ($requestWithLessStatusCode === null || $request->statusCode < $requestWithLessStatusCode->statusCode) {
                $requestWithLessStatusCode = $request;
            }
        }

        return $requestWithLessStatusCode;
    }
}