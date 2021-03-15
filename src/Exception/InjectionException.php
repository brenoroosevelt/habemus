<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Psr\Container\ContainerExceptionInterface;
use ReflectionParameter;
use ReflectionProperty;

class InjectionException extends \Exception implements ContainerExceptionInterface
{

    public static function notAnObject($value)
    {
        return new static(
            sprintf(
                "Expected object to inject property dependencies. Got (%s).",
                gettype($value)
            )
        );
    }

    public static function unresolvablePropertyInjection(ReflectionProperty $property, $object): self
    {
        return new static(
            sprintf(
                "Cannot resolve the property (%s) injection in (%s)",
                $property->getName(),
                get_class($object)
            )
        );
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $propertyOrParameter
     * @return self
     */
    public static function indeterminateInjection($propertyOrParameter): self
    {
        $type = $propertyOrParameter instanceof ReflectionProperty ? "property" : "parameter";
        return new static(
            sprintf(
                "Cannot determine the %s injection for (%s) in (%s).",
                $type,
                $propertyOrParameter->getName(),
                $propertyOrParameter->getDeclaringClass()->getName()
            )
        );
    }
}
