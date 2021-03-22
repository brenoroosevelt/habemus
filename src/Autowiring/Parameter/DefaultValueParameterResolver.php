<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class DefaultValueParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        if ($parameter->isDefaultValueAvailable()) {
            $result[] = $parameter->getDefaultValue();
            return true;
        }

        return false;
    }
}
