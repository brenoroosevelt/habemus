<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Exception;
use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\Identifiable;
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
                "The (%s) cannot to call the method (%s::%s).",
                self::format($definition),
                gettype($instance),
                $method
            )
        );
    }

    public static function unavailableConstructorParameters(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The (%s) does not accept constructor parameters.",
                self::format($definition)
            )
        );
    }

    public static function unavailableMethodCall(Definition $definition): self
    {
        return new static(
            $definition,
            sprintf(
                "The (%s) does not accept method calls.",
                self::format($definition)
            )
        );
    }

    public static function unshareable(Definition $definition): self
    {
        return new static($definition, sprintf("The (%s) is not shareable.", self::format($definition)));
    }

    public static function untaggable(Definition $definition): self
    {
        return new static($definition, sprintf("The (%s) is not taggable.", self::format($definition)));
    }

    protected static function format(Definition $definition): string
    {
        $type = get_class($definition);
        $id = $definition instanceof Identifiable && $definition->getIdentity();
        $format = "definition of " . ($id !== null ? "id" : "type") . "(%s).";
        return sprintf($format, ($id !== null ? $id : $type));
    }
}
