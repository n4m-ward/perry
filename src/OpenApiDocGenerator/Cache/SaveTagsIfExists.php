<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\Tag\Tag;
use Perry\Files\Storage;
use Perry\Helpers\Tests\TestInfoResolver;

class SaveTagsIfExists
{
    public function execute(): void
    {
        $testInfo = TestInfoResolver::resolve();
        $securitySchemes = $this->findAllTagsFromTestCase($testInfo->getReflectionClass());
        if (empty($securitySchemes)) {
            return;
        }

        Storage::saveTags($securitySchemes);
    }

    private function findAllTagsFromTestCase(\ReflectionClass $testCase): array
    {
        $securitySchemes = $this->mapTagToInstance($testCase->getAttributes(Tag::class));

        if ($testCase->getParentClass()) {
            $securitySchemes = array_merge(
                $securitySchemes,
                $this->findAllTagsFromTestCase($testCase->getParentClass())
            );
        }

        return $securitySchemes;
    }

    /**
     * @param \ReflectionAttribute[] $securitySchemes
     * @return Tag[]
     */
    private function mapTagToInstance(array $securitySchemes): array
    {
        return array_map(fn (\ReflectionAttribute $securityScheme) => $securityScheme->newInstance(), $securitySchemes);
    }
}
