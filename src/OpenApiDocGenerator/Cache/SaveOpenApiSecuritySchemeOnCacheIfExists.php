<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Files\Storage;
use Perry\Helpers\Tests\TestInfoResolver;

class SaveOpenApiSecuritySchemeOnCacheIfExists
{
    public function execute(): void
    {
        $testInfo = TestInfoResolver::resolve();
        $securitySchemes = $this->findAllSecuritySchemesFromTestCase($testInfo->getReflectionClass());
        if (empty($securitySchemes)) {
            return;
        }

        Storage::saveSecuritySchemes($securitySchemes);
    }

    private function findAllSecuritySchemesFromTestCase(\ReflectionClass $testCase): array
    {
        $securitySchemes = $this->mapSecuritySchemeToInstance($testCase->getAttributes(SecurityScheme::class));

        if ($testCase->getParentClass()) {
            $securitySchemes = array_merge(
                $securitySchemes,
                $this->findAllSecuritySchemesFromTestCase($testCase->getParentClass())
            );
        }

        return $securitySchemes;
    }

    /**
     * @param \ReflectionAttribute[] $securitySchemes
     * @return SecurityScheme[]
     */
    private function mapSecuritySchemeToInstance(array $securitySchemes): array
    {
        return array_map(fn (\ReflectionAttribute $securityScheme) => $securityScheme->newInstance(), $securitySchemes);
    }
}
