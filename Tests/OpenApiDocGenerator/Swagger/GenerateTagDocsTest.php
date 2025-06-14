<?php

namespace Tests\OpenApiDocGenerator\Swagger;

use Perry\Attributes\Tag\Tag;
use Perry\Files\Storage;
use Perry\OpenApiDocGenerator\OpenApi\GenerateTagDocs;
use Tests\Base\BaseTestCase;
use Tests\Base\RemoveSwaggerAfterTests;

class GenerateTagDocsTest extends BaseTestCase
{
    use RemoveSwaggerAfterTests;

    private GenerateTagDocs $generateTagDocs;

    public function setUp(): void
    {
        parent::setUp();
        $this->generateTagDocs = new GenerateTagDocs();
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
