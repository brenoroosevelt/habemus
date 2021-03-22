<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class OptionalParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        return $parameter->isOptional();
    }
}
