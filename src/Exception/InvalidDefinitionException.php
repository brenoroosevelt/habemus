<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class InvalidDefinitionException extends Exception implements ContainerExceptionInterface
{
    public static function unavailable($name): self
    {
        return new static(
            sprintf(
                "The definition of type (%s) is invalid or unavailable.",
                $name
            )
        );
    }
}
