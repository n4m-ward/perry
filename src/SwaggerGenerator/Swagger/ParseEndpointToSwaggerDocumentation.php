<?php

namespace Perry\SwaggerGenerator\Swagger;

use Perry\Files\Storage;
use Perry\SwaggerGenerator\Cache\Dtos\TestRequestDto;
use Perry\SwaggerGenerator\Helper\SwaggerObjectHelper;

class ParseEndpointToSwaggerDocumentation
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
        $requestWithLessStatusCode = $this->findRequestWithLessStatusCode($requestList);
        $requestDescription = $this->formatTestNameToRequestDescription($requestWithLessStatusCode->testName);

        $output = [
            'summary' => $requestDescription,
            'description' => $requestDescription,
            'operationId' => $requestWithLessStatusCode->testName,
            'responses' => $this->formatSwaggerResponses($requestList)
        ];
        if(!empty($requestWithLessStatusCode->body)) {
            $output['requestBody'] = [
                'description' => $requestDescription,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => SwaggerObjectHelper::formatToSwagger($requestWithLessStatusCode->body)
                        ]
                    ]
                ],
            ];
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

    private function addPropertiesToResponses(array $responses): array
    {
        if(empty($responses['response'] ?? [])) {
            return [];
        }

        return [
            'type' => 'object',
            'properties' => SwaggerObjectHelper::formatToSwagger($responses['response'] ?? [])
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
            $output[(string) $request] = Storage::getSingleTestRequest($endpoint, $method, 200);
        }

        return $output;
    }

    /**
     * @param TestRequestDto[] $requestList
     */
    private function findRequestWithLessStatusCode(array $requestList): TestRequestDto
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