<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class NullableParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$resolved, array &$result): void
    {
        $name = $parameter->getName();
        if (array_key_exists($name, $resolved)) {
            return;
        }

        if (!$parameter->isOptional() && $parameter->allowsNull()) {
            $resolved[$name] = true;
            $result[] = null;
        }
    }
}
