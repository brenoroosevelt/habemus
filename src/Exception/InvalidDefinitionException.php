<?php
declare(strict_types=1);

namespace Habemus\Exception;

class InvalidDefinitionException extends ContainerException
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
