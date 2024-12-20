<?php

namespace Tests\ControllerWrapper;

use Illuminate\Http\Request;
use Perry\ControllerWrapper\ControllerWrapper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Tests\Dummy\DummyController;

class ControllerWrapperTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_wrapperShouldCallControllerMethod(): void
    {
        $dummyControllerMock = $this->createMock(DummyController::class);
        $wrappedController = ControllerWrapper::wrap($dummyControllerMock);

        $dummyControllerMock->expects($this->once())->method('dummyRequest');
        $wrappedController->dummyRequest(new Request());
    }
}
