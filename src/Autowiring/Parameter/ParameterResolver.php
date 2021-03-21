<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

interface ParameterResolver
{
    /**
     * @param ReflectionParameter $parameter
     * @param array $arguments pre defined arguments
     * @param array $resolved resolved arguments indexed by name
     * @param array $result
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$resolved, array &$result): void;
}
