<?php


namespace Habemus\Autowire;

use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

final class Reflector
{
    /**
     * @param ReflectionParameter|ReflectionProperty $subject
     * @param bool $primitives
     * @return string|null
     */
    public static function getTypeHint($subject, bool $primitives = true): ?string
    {
        $type = $subject->getType();
        if (! $type instanceof ReflectionNamedType) {
            return null;
        }

        if ($type->isBuiltin() && !$primitives) {
            return null;
        }

        $typeHint = ltrim($type->getName(), "?");
        if ($typeHint === 'self') {
            $typeHint = $subject->getDeclaringClass()->getName();
        }

        return $typeHint;
    }

    /**
     * @param ReflectionParameter|ReflectionProperty $subject
     * @param string $attribute
     * @return null
     */
    public static function getFirstAttribute($subject, string $attribute)
    {
        self::assertAttributesAvailable();
        $attribute = $subject->getAttributes($attribute)[0] ?? null;
        return $attribute !== null ? $attribute->newInstance() : null;
    }

    public static function attributesAvailable(): bool
    {
        return PHP_VERSION_ID >= 80000;
    }

    public static function assertAttributesAvailable(): void
    {
        if (!self::attributesAvailable()) {
            throw new \RuntimeException(
                "Attributes are not available. Use a PHP version >=8.0 to enable attribute injection support"
            );
        }
    }
}
