<?php

namespace Tests\PerryHttp;

use Illuminate\Support\Facades\Route;
use Perry\Attributes\Tag\UsingTag;
use Perry\PerryHttp\PerryHttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveDocumentationAfterTests;
use Tests\Dummy\DummyController;
use Tests\Dummy\DummyControllerMock;

class PerryHttpTest extends BaseTestCase
{
    use PerryHttpRequest;
    use RemoveDocumentationAfterTests;

    #[UsingTag('Tag 1')]
    public function test_shouldPost_withHeadersAndBody(): void
    {
        Route::post('/user', [DummyController::class, 'dummyRequest']);
        DummyControllerMock::mockHttpResponse([
            'name' => 'John Doe',
            'age' => 25,
            'email' => 'john@doe.com',
            'password' => 'password'
        ], Response::HTTP_CREATED);

        $request = $this->perryHttp()
            ->withHeaders([
                'Accept' => 'application/json',
                'bearer' => 'token',
            ])
            ->withBody([
                'name' => 'John Doe',
                'age' => 25,
                'email' => 'john@doe.com',
                'password' => 'password',
            ])->post('/user');

        $this->assertEquals('John Doe', $request->json('name'));
        $this->assertEquals(25, $request->json('age'));
        $this->assertEquals('john@doe.com', $request->json('email'));
        $this->assertEquals('password', $request->json('password'));
    }
}
