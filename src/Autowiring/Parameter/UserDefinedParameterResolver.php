<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class UserDefinedParameterResolver implements ParameterResolver
{
    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$resolved, array &$result): void
    {
        $name = $parameter->getName();
        if (!array_key_exists($parameter->getName(), $arguments)) {
            return;
        }

        $resolved[$name] = true;
        if ($parameter->isVariadic()) {
            $_argument = !is_array($arguments[$name]) ? [$arguments[$name]] : $arguments[$name];
            $result =  array_merge($result, $_argument);
        } else {
            $result[] = $arguments[$name];
        }
    }
}
