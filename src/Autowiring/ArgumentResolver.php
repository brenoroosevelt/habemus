<?php
declare(strict_types=1);

namespace Habemus\Autowiring;

use ReflectionFunctionAbstract;

interface ArgumentResolver
{
    /**
     * @param ReflectionFunctionAbstract $function
     * @param array $arguments
     * @return array
     */
    public function resolveArguments(ReflectionFunctionAbstract $function, array $arguments = []): array;
}
