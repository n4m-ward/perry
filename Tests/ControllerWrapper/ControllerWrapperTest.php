<?php

namespace Tests\ControllerWrapper;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Perry\ControllerWrapper\ControllerWrapper;
use Perry\SwaggerGenerator\SwaggerGenerator;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Dummy\DummyController;

class ControllerWrapperTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_wrapperShouldCallControllerMethod(): void
    {
        $dummyControllerMock = $this->createMock(DummyController::class);
        $swaggerGeneratorMock = $this->createMock(SwaggerGenerator::class);

        $request = new Request();
        $dummyControllerMock->expects($this->once())->method('dummyRequest')->willReturn($response = response()->json([]));
        $swaggerGeneratorMock->expects($this->once())->method('generateDocAndSaveOnCache')->with([$request], $response);

        $this->app->instance(SwaggerGenerator::class, $swaggerGeneratorMock);

        $wrappedController = ControllerWrapper::wrap($dummyControllerMock);

        $wrappedController->dummyRequest($request);
    }
}
