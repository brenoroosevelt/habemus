<?php
declare(strict_types=1);

namespace Habemus\Exception;

use ReflectionParameter;
use ReflectionProperty;

class InjectionException extends ContainerException
{

    public static function notAnObject($value)
    {
        return new self(
            sprintf(
                "Expected object to inject property dependencies. Got (%s).",
                gettype($value)
            )
        );
    }

    public static function unresolvablePropertyInjection(ReflectionProperty $property, $object): self
    {
        return new self(
            sprintf(
                "Cannot resolve the injection for property ($%s) in (%s).",
                $property->getName(),
                get_class($object)
            )
        );
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $propertyOrParameter
     * @return self
     */
    public static function invalidInjection($propertyOrParameter): self
    {
        $type = $propertyOrParameter instanceof ReflectionProperty ? "property" : "constructor parameter";
        return new self(
            sprintf(
                "Impossible to determine the injection for %s ($%s) in (%s).",
                $type,
                $propertyOrParameter->getName(),
                $propertyOrParameter->getDeclaringClass()->getName()
            )
        );
    }
}
