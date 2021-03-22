<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

interface ParameterResolver
{
    /**
     * @param ReflectionParameter $parameter
     * @param array $arguments pre defined arguments
     * @param array $result
     * @return bool returns 'true' if the parameter has been resolved
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool;
}
