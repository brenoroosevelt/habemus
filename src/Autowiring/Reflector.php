<?php
declare(strict_types=1);

namespace Habemus\Autowiring;

use Habemus\Exception\ContainerException;
use Habemus\Utility\PHPVersion;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class Reflector
{
    /**
     * @param ReflectionParameter|ReflectionProperty $subject
     * @param bool $detectPrimitiveTypes
     * @return string|null
     */
    public function getTypeHint($subject, bool $detectPrimitiveTypes = true): ?string
    {
        $type = $subject->getType();
        if (! $type instanceof ReflectionNamedType) {
            return null;
        }

        if ($type->isBuiltin() && !$detectPrimitiveTypes) {
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
     * @return mixed
     * @throws ContainerException
     */
    public function getFirstAttribute($subject, string $attribute)
    {
        $this->assertAttributesAvailable();
        $attribute = $subject->getAttributes($attribute)[0] ?? null;
        if (empty($attribute)) {
            return null;
        }

        return $attribute->newInstance();
    }

    public function attributesAvailable(): bool
    {
        return PHPVersion::current() >= PHPVersion::V8_0;
    }

    /**
     * @throws ContainerException
     */
    public function assertAttributesAvailable(): void
    {
        if (!$this->attributesAvailable()) {
            throw new ContainerException(
                "Attributes injection are not available. Use a PHP version >=8.0."
            );
        }
    }
}
