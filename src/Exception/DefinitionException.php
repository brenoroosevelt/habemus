<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Habemus\Definition\Definition;

class DefinitionException extends ContainerException
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
                "The definition of %s (%s) cannot to call the method (%s::%s).",
                $definition->getIdentity() ? "id" : "type",
                $definition->getIdentity() ?? get_class($definition),
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
                "The definition of %s (%s) does not accept constructor parameters.",
                $definition->getIdentity() ? "id" : "type",
                $definition->getIdentity() ?? get_class($definition)
            )
        );
    }

    public static function unavailableMethodCall(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of %s (%s) does not accept method calls.",
                $definition->getIdentity() ? "id" : "type",
                $definition->getIdentity() ?? get_class($definition)
            )
        );
    }

    public static function unshareable(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of %s (%s) is not shareable.",
                $definition->getIdentity() ? "id" : "type",
                $definition->getIdentity() ?? get_class($definition)
            )
        );
    }

    public static function untaggable(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The definition of %s (%s) is not taggable.",
                $definition->getIdentity() ? "id" : "type",
                $definition->getIdentity() ?? get_class($definition)
            )
        );
    }
}
