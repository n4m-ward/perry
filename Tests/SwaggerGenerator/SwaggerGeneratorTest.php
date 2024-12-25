<?php

namespace Tests\SwaggerGenerator;

use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Exceptions\PerryStorageException;
use Perry\Files\Storage;
use Perry\SwaggerGenerator\SwaggerGenerator;
use ReflectionException;
use Tests\Base\BaseTestCase;

class SwaggerGeneratorTest extends BaseTestCase
{
    private SwaggerGenerator $swaggerGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->swaggerGenerator = new SwaggerGenerator();
    }

    /**
     * @throws PerryStorageException
     * @throws PerryInfoAttributeNotFoundException
     * @throws ReflectionException
     */
    public function test_shouldGenerateRootApiDocumentation(): void
    {
        $this->swaggerGenerator->generateDocAndSaveOnCache([], response()->json());
        $rootInfo = Storage::getRootInfo();

        $this->assertEquals('1.0.0', $rootInfo->info->version);
        $this->assertEquals('Example server title', $rootInfo->info->title);
        $this->assertEquals('Example server description', $rootInfo->info->description);

        $this->assertEquals('Server 1', $rootInfo->servers->servers[0]->description);
        $this->assertEquals('Server 2', $rootInfo->servers->servers[1]->description);
        $this->assertEquals('https://server1.com', $rootInfo->servers->servers[0]->url);
        $this->assertEquals('https://server2.com', $rootInfo->servers->servers[1]->url);
    }
}
