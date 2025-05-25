<?php

namespace PerryTest;

use Illuminate\Support\Facades\Route;
use Perry\PerryHttp\PerryHttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Tests\Base\BaseTestCase;
use Tests\Dummy\DummyController;
use Tests\Dummy\DummyControllerMock;

class DummyControllerTest extends BaseTestCase
{
    use PerryHttpRequest;

    public function test_shouldCreateUser(): void
    {
        Route::post('/user', [DummyController::class, 'dummyRequest']);

        DummyControllerMock::mockHttpResponse(['success' => true], Response::HTTP_CREATED);

        $response = $this->perryHttp()
            ->withBody([
            'name' => 'John Doe',
            'age' => 25,
            'email' => 'john@doe.com',
            'password' => 'password',
        ])->post('/user');

        $response = json_decode($response->getContent(), true);

        $this->assertTrue($response['success']);
    }

    public function test_shouldReturnUser(): void
    {
        Route::get('/user/{user_id}', [DummyController::class, 'dummyRequest']);

        DummyControllerMock::mockHttpResponse([
            'name' => 'John Doe',
            'age' => 25,
            'email' => 'john@doe.com',
            'permissions' => ['CREATE_USER', 'UPDATE_USER'],
        ], Response::HTTP_OK);

        $response = $this->get('/user/123', headers: [
            'Accept' => 'application/json',
            'bearer' => 'token',
        ]);

        $responseJson = json_decode($response->getContent(), true);

        $this->assertEquals('John Doe', $responseJson['name']);
        $this->assertEquals(25, $responseJson['age']);
        $this->assertEquals('john@doe.com', $responseJson['email']);
        $this->assertEquals(['CREATE_USER', 'UPDATE_USER'], $responseJson['permissions']);
    }
}