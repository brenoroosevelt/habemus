<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Exception;
use Habemus\Definition\Definition;
use Psr\Container\ContainerExceptionInterface;

class DefinitionException extends Exception implements ContainerExceptionInterface
{
    /**
     * @var Definition
     */
    protected $definition;

    public function __construct(Definition $definition, $message = "", $code = 0, $previous = null)
    {
        $this->definition = $definition;
        parent::__construct($message, $code, $previous);
    }

    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    public static function invalidMethodCall(Definition $definition, $instance, $method): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of id (%s) cannot to call the method (%s::%s).",
                $definition->getIdentity(),
                is_object($instance) ? get_class($instance) : gettype($instance),
                $method
            )
        );
    }

    public static function unavailableConstructorParameters(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of id (%s) does not accept constructor parameters.",
                $definition->getIdentity()
            )
        );
    }

    public static function unavailableMethodCall(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of id (%s) does not accept method calls.",
                $definition->getIdentity()
            )
        );
    }

    public static function unshareable(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf("The definition of id (%s) is not shareable.", $definition->getIdentity())
        );
    }

    public static function untaggable(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf("The definition of id (%s) is not taggable.", $definition->getIdentity())
        );
    }
}
