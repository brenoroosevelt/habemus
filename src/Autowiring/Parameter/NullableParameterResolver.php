<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class NullableParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        if (!$parameter->isOptional() && $parameter->allowsNull()) {
            $result[] = null;
            return true;
        }

        return false;
    }
}
