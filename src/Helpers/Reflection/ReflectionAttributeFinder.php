<?php

namespace Perry\Helpers\Reflection;


class ReflectionAttributeFinder
{
    /**
     * @param class-string<AttributeClass> $attributeClass
     * @return AttributeClass[]
     * @throws \ReflectionException
     * @template AttributeClass
     */
    public static function findAttributesForClassOrBaseClass(string $attributeClass, \ReflectionClass $class): array
    {
        $classAttributes = self::mapReflectionAttributeToInstance($class->getAttributes($attributeClass));

        if ($class->getParentClass()) {
            $classAttributes = array_merge(
                $classAttributes,
                self::findAttributesForClassOrBaseClass($attributeClass, $class->getParentClass())
            );
        }

        return $classAttributes;
    }

    private static function mapReflectionAttributeToInstance(array $usingSecuritySchemes): array
    {
        return array_map(fn (\ReflectionAttribute $usingSecurityScheme) => $usingSecurityScheme->newInstance(), $usingSecuritySchemes);
    }
}
