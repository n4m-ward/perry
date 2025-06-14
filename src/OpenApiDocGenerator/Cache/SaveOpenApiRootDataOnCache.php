<?php

namespace Perry\OpenApiDocGenerator\Cache;

use Perry\Attributes\Info;
use Perry\Attributes\Servers;
use Perry\Exceptions\PerryInfoAttributeNotFoundException;
use Perry\Files\Storage;
use Perry\Helpers\Tests\TestInfoResolver;
use Perry\OpenApiCache\OpenApiRootInfo;

class SaveOpenApiRootDataOnCache
{
    /**
     * @throws \ReflectionException
     * @throws PerryInfoAttributeNotFoundException
     */
    public function execute(): void
    {
        $testInfo = TestInfoResolver::resolve();
        $classWithSwaggerRootInfo = $this->getClassWithRootInfo($testInfo->getReflectionClass());
        $info = $classWithSwaggerRootInfo->getAttributes(Info::class)[0]->newInstance();
        $servers = $classWithSwaggerRootInfo->getAttributes(Servers::class)[0]?->newInstance() ?? null;

        $rootInfoCache = new OpenApiRootInfo(
            info: $info,
            servers: $servers,
        );

        Storage::saveTestRootInfo($rootInfoCache);
    }

    /**
     * @throws PerryInfoAttributeNotFoundException
     */
    public function getClassWithRootInfo(\ReflectionClass $reflectionClass): \ReflectionClass
    {
        $infoAttributes = $reflectionClass->getAttributes(Info::class);
        if(!empty($infoAttributes)) {
            return $reflectionClass;
        }

        $baseClass = $reflectionClass->getParentClass();
        if(!$baseClass) {
            throw new PerryInfoAttributeNotFoundException('attribute '. Info::class. ' not found');
        }

        return $this->getClassWithRootInfo($baseClass);
    }
}
