<?php

namespace PerryTest\Perry;

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

        $this
            ->perryHttp()
            ->withBody([
                'name' => 'John Doe',
                'age' => 25,
                'email' => 'john@doe.com',
                'password' => 'password',
            ])
            ->post('/user')
            ->assertJson(['success' => true])
            ->assertStatus(Response::HTTP_CREATED);
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

        $this
            ->perryHttp()
            ->withHeaders([
                'Accept' => 'application/json',
                'bearer' => 'token',
            ])
            ->get('/user/123')
            ->assertJson([
                'name' => 'John Doe',
                'age' => 25,
                'email' => 'john@doe.com',
                'permissions' => ['CREATE_USER', 'UPDATE_USER'],
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_shouldReturnUserArray(): void
    {
        Route::get('/users', [DummyController::class, 'dummyRequest']);

        DummyControllerMock::mockHttpResponse($response = [
            'total' => '1',
            'items' => [
                [
                    'name' => 'John Doe',
                    'age' => 25,
                    'email' => 'john@doe.com',
                    'permissions' => ['CREATE_USER', 'UPDATE_USER'],
                ]
            ],
        ], Response::HTTP_OK);

        $this
            ->perryHttp()
            ->withHeaders([
                'Accept' => 'application/json',
                'bearer' => 'token',
            ])
            ->get('/users')
            ->assertJson($response)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_shouldUpdateUser(): void
    {
        Route::put('/user/123', [DummyController::class, 'dummyRequest']);

        DummyControllerMock::mockHttpResponse(['success' => true], Response::HTTP_OK);

        $this
            ->perryHttp()
            ->withBody([
                'name' => 'John Doe',
                'age' => 25,
                'email' => 'john@doe.com',
                'password' => 'password',
            ])
            ->put('/user/123')
            ->assertJson(['success' => true])
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_shouldDeleteUser(): void
    {
        Route::delete('/user/{user_id}', [DummyController::class, 'dummyRequest']);

        DummyControllerMock::mockHttpResponse([], Response::HTTP_OK);

        $this
            ->perryHttp()
            ->delete('/user/123')
            ->assertJson([])
            ->assertStatus(Response::HTTP_OK);
    }
}