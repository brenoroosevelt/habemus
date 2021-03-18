<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Closure;
use Habemus\Container;
use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Definition\Build\ClassDefinition;
use Habemus\Definition\Build\FnDefinition;
use Habemus\Definition\Build\RawDefinition;

class AutoDetection implements DefinitionDetection
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function detect($value): Definition
    {
        if ($value instanceof Definition) {
            return $value;
        }

        if (is_null($value)) {
            return new RawDefinition($value);
        }

        if (!is_scalar($value) && !is_array($value) && !is_resource($value) && get_class($value) === Closure::class) {
            return new FnDefinition($value);
        }

        if ($this->container->autowireEnabled() && !is_object($value) && is_string($value) && class_exists($value)) {
            return new ClassDefinition($value);
        }

        if (is_array($value)) {
            $hasDefinitionInside = false;
            array_walk_recursive($value, function ($item) use (&$hasDefinitionInside) {
                if ($item instanceof ReferenceDefinition) {
                    $hasDefinitionInside = true;
                }
            });
            if ($hasDefinitionInside) {
                return new ArrayDefinition($value, true);
            }
        }

        return new RawDefinition($value);
    }
}
