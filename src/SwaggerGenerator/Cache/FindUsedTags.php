<?php

namespace Perry\SwaggerGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Attributes\Tag\Tag;
use Perry\Attributes\Tag\UsingTag;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Helpers\Reflection\ReflectionAttributeFinder;
use Perry\Helpers\Tests\TestInfoResolver;

class FindUsedTags
{
    /**
     * @return UsingTag[]
     * @throws \ReflectionException|PerryAttributeNotFoundException
     */
    public function execute(): array
    {
        $testInfo = TestInfoResolver::resolve();
        $tagsFromMethod = $this->mapAttributeToInstance($testInfo->getReflectionMethod()->getAttributes(UsingTag::class));
        $tagsFromTestCase = ReflectionAttributeFinder::findAttributesForClassOrBaseClass(UsingTag::class, $testInfo->getReflectionClass());
        $usedTags = array_merge($tagsFromMethod, $tagsFromTestCase);

        $securitySchemes = ReflectionAttributeFinder::findAttributesForClassOrBaseClass(Tag::class, $testInfo->getReflectionClass());

        $this->validateTagWasImplemented($usedTags, $securitySchemes);

        return $usedTags;
    }

    /**
     * @param UsingTag[] $usedTags
     * @param Tag[] $tagsOnBaseTestCase
     * @throws PerryAttributeNotFoundException
     */
    private function validateTagWasImplemented(array $usedTags, array $tagsOnBaseTestCase): void
    {
        foreach ($usedTags as $usedTag) {
            if (!$this->tagWasImplemented($usedTag, $tagsOnBaseTestCase)) {
                throw new PerryAttributeNotFoundException("Tag [$usedTag->name] was not implemented");
            }
        }
    }

    /**
     * @param Tag[] $tagsFromTestCase
     */
    private function tagWasImplemented(UsingTag $usedTag, array $tagsFromTestCase): bool
    {
        $tag = array_find($tagsFromTestCase, fn (Tag $tag) => $tag->name === $usedTag->name);
        return !empty($tag);
    }

    private function mapAttributeToInstance(array $usedTags): array
    {
        return array_map(fn (\ReflectionAttribute $usedTag) => $usedTag->newInstance(), $usedTags);
    }
}
