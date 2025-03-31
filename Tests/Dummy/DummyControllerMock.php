<?php

namespace Tests\Dummy;

class DummyControllerMock
{
    private static array $httpResponse = [
        'body' => null,
        'statusCode' => 200,
    ];

    public static function mockHttpResponse(mixed $body, int $statusCode): void
    {
        self::$httpResponse = [
            'body' => $body,
            'statusCode' => $statusCode,
        ];
    }

    /**
     * @return array{body: mixed, statusCode: int}
     */
    public static function getResponse(): array
    {
        return self::$httpResponse;
    }

    public static function clearResponse(): void
    {
        self::$httpResponse = [
            'body' => null,
            'statusCode' => 200,
        ];
    }
}