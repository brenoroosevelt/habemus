<?php
declare(strict_types=1);

namespace Habemus\Exception;

use ReflectionFunctionAbstract;
use ReflectionMethod;

class UnresolvableParameterException extends ContainerException
{
    public static function createForFunction(ReflectionFunctionAbstract $function, string $parameter) : self
    {
        if ($function instanceof ReflectionMethod) {
            return new self(
                sprintf(
                    "Container cannot resolve parameter (%s) in (%s::%s)",
                    $parameter,
                    $function->getDeclaringClass(),
                    $function->getName()
                )
            );
        }

        return new self(
            sprintf("Container cannot resolve parameter (%s)", $parameter)
        );
    }
}
