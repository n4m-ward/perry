<?php

namespace Tests\SwaggerGenerator\Cache;

use Perry\Attributes\Tag\Tag;
use Perry\Attributes\Tag\UsingTag;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\SwaggerGenerator\Cache\FindUsedTags;
use Tests\Base\BaseTestCase;

#[Tag(name: 'Tag 1', description: 'Tag 1 description')]
#[Tag('Tag 2', description: 'Tag 2 description')]
#[Tag('Tag 3', description: 'Tag 3 description')]
class FindUsedTagsTest extends BaseTestCase
{
    private FindUsedTags $findUsedTags;

    public function setUp(): void
    {
        parent::setUp();
        $this->findUsedTags = new FindUsedTags();
    }

    public function test_shouldNotFindUsedTags(): void
    {
        $usedTags = $this->findUsedTags->execute();

        $this->assertEmpty($usedTags);
    }

    #[UsingTag('Tag 1')]
    #[UsingTag('Tag 2')]
    #[UsingTag('Tag 3')]
    public function test_shouldFindUsedTags(): void
    {
        $usedTags = $this->findUsedTags->execute();

        $this->assertEquals([
            new UsingTag('Tag 1'),
            new UsingTag('Tag 2'),
            new UsingTag('Tag 3'),
        ], $usedTags);
    }

    #[UsingTag('Not implemented')]
    public function test_shouldThrowException_whenTagIsNotImplemented(): void
    {
        $this->expectException(PerryAttributeNotFoundException::class);
        $this->expectExceptionMessage("Tag [Not implemented] was not implemented");

        $this->findUsedTags->execute();
        $this->fail('PerryAttributeNotFoundException should be thrown');
    }
}