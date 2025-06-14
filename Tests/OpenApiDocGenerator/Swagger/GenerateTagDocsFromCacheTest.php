<?php

namespace Tests\OpenApiDocGenerator\Swagger;

use Perry\Attributes\Tag\Tag;
use Perry\Files\Storage;
use Perry\OpenApiDocGenerator\OpenApi\GenerateTagDocsFromCache;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveSwaggerAfterTests;

class GenerateTagDocsFromCacheTest extends BaseTestCase
{
    use RemoveSwaggerAfterTests;

    private GenerateTagDocsFromCache $generateTagDocs;

    public function setUp(): void
    {
        parent::setUp();
        $this->generateTagDocs = new GenerateTagDocsFromCache();
    }

    public function test_shouldGenerateTagDocs(): void
    {
        Storage::saveTags([
            new Tag(name: 'foo', description: 'bar'),
            new Tag(name: 'fizz', description: 'buzz'),
        ]);

        $tagDocs = $this->generateTagDocs->execute();

        $this->assertEquals([
            ['name' => 'foo', 'description' => 'bar'],
            ['name' => 'fizz', 'description' => 'buzz'],
        ], $tagDocs);
    }

    public function test_shouldGenerateEmptyArray_whenHasNoTags(): void
    {
        $tagDocs = $this->generateTagDocs->execute();

        $this->assertEquals([], $tagDocs);
    }
}
