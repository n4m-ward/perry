<?php

namespace Tests\Base;

use Perry\Files\Storage;

trait RemoveDocumentationAfterTests
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::deleteCacheFolder();
        Storage::deleteSwaggerFolder();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Storage::deleteCacheFolder();
        Storage::deleteSwaggerFolder();
    }
}
