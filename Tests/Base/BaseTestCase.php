<?php

namespace Tests\Base;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Orchestra\Testbench\TestCase;
use Perry\Attributes\Info;
use Perry\Attributes\Server;
use Perry\Attributes\Servers;
use Perry\ProjectExecutionMode;
use Perry\UnitTest\ProjectExecutionModeOverride;

#[Servers(
    new Server(description: 'Server 1', url: 'https://server1.com'),
    new Server(description: 'Server 2', url: 'https://server2.com'),
)]
#[Info(
    version: '1.0.0',
    title: 'Example server title',
    description: 'Example server description',
)]
class BaseTestCase extends TestCase
{
    use MakesHttpRequests;

    protected function setUp(): void
    {
        parent::setUp();
        ProjectExecutionModeOverride::set(ProjectExecutionMode::PROJECT_UNIT_TEST);
    }
}
