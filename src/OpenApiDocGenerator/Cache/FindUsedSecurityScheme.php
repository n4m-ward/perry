<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Perry\Attributes\SecurityScheme\SecurityScheme;
use Perry\Attributes\SecurityScheme\UseSecurityScheme;
use Perry\Exceptions\PerryAttributeNotFoundException;
use Perry\Helpers\Reflection\ReflectionAttributeFinder;
use Perry\Helpers\Tests\TestInfoResolver;

class FindUsedSecurityScheme
{
    /**
     * @return UseSecurityScheme[]
     * @throws \ReflectionException|PerryAttributeNotFoundException
     */
    public function execute(): array
    {
        $testInfo = TestInfoResolver::resolve();
        $useSecuritySchemesFromMethod = $this->mapAttributeToInstance($testInfo->getReflectionMethod()->getAttributes(UseSecurityScheme::class));
        $useSecuritySchemesFromTestCase = ReflectionAttributeFinder::findAttributesForClassOrBaseClass(UseSecurityScheme::class, $testInfo->getReflectionClass());
        $useSecuritySchemes = array_merge($useSecuritySchemesFromMethod, $useSecuritySchemesFromTestCase);

        $securitySchemes = ReflectionAttributeFinder::findAttributesForClassOrBaseClass(SecurityScheme::class, $testInfo->getReflectionClass());

        $this->validateSecuritySchemeWasImplemented($useSecuritySchemes, $securitySchemes);

        return $useSecuritySchemes;
    }

    /**
     * @param UseSecurityScheme[] $useSecuritySchemes
     * @param SecurityScheme[] $securitySchemes
     * @throws PerryAttributeNotFoundException
     */
    private function validateSecuritySchemeWasImplemented(array $useSecuritySchemes, array $securitySchemes): void
    {
        foreach ($useSecuritySchemes as $useSecurityScheme) {
            if (!$this->useSecuritySchemeWasImplemented($useSecurityScheme, $securitySchemes)) {
                throw new PerryAttributeNotFoundException("SecurityScheme [$useSecurityScheme->securityScheme] was not implemented");
            }
        }
    }

    /**
     * @param SecurityScheme[] $securitySchemes
     */
    private function useSecuritySchemeWasImplemented(UseSecurityScheme $useSecurityScheme, array $securitySchemes): bool
    {
        $securityScheme = array_find($securitySchemes, fn (SecurityScheme $securityScheme) => $securityScheme->securityScheme === $useSecurityScheme->securityScheme);
        return !empty($securityScheme);
    }

    private function mapAttributeToInstance(array $usingSecuritySchemes): array
    {
        return array_map(fn (\ReflectionAttribute $usingSecurityScheme) => $usingSecurityScheme->newInstance(), $usingSecuritySchemes);
    }
}
