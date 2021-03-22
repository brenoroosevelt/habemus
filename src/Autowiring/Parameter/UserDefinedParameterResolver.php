<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class UserDefinedParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        $name = $parameter->getName();
        if (!array_key_exists($name, $arguments)) {
            return false;
        }

        return (new VariadicParameterResolver($arguments[$name]))->resolve($parameter, $arguments, $result);
    }
}
