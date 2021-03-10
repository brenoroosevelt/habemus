<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Closure;
use Habemus\Container;
use Habemus\Definition\Available\ArrayDefinition;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Definition\Definition;
use Habemus\Definition\DefinitionDetection;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;

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

        if (!is_scalar($value) && !is_array($value) && !is_resource($value) && get_class($value) === Closure::class) {
            return new FnDefinition($value);
        }

        if ($this->container->autowireEnabled() && !is_object($value) && is_string($value) && class_exists($value)) {
            return new ClassDefinition($value);
        }

        if (is_array($value)) {
            $hasDefinition = false;
            array_walk_recursive($value, function ($item) use (&$hasDefinition) {
                if ($item instanceof IdDefinition) {
                    $hasDefinition = true;
                }
            });
            if ($hasDefinition) {
                return new ArrayDefinition($value, true);
            }
        }

        return new RawDefinition($value);
    }
}
