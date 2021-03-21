<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Closure;
use Habemus\Autowiring\ClassResolver;
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

    /**
     * @var ClassResolver
     */
    protected $classResolver;

    public function __construct(Container $container, ClassResolver $classResolver)
    {
        $this->container = $container;
        $this->classResolver = $classResolver;
    }

    public function detect($value): Definition
    {
        if ($value instanceof Definition) {
            return $value;
        }

        if (is_null($value)) {
            return new RawDefinition($value);
        }

        if ($this->isClosure($value)) {
            return new FnDefinition($value);
        }

        if ($this->isAutowire($value)) {
            return (new ClassDefinition($value))->setClassResolver($this->classResolver);
        }

        if ($this->isArrayDefinition($value)) {
            return new ArrayDefinition($value, true);
        }

        return new RawDefinition($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isClosure($value): bool
    {
        return !is_scalar($value) && !is_array($value) && !is_resource($value) && get_class($value) === Closure::class;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isAutowire($value): bool
    {
        return $this->container->autowireEnabled() && !is_object($value) && is_string($value) && class_exists($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isArrayDefinition($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $hasDefinitionInside = false;
        array_walk_recursive($value, function ($item) use (&$hasDefinitionInside) {
            if ($item instanceof ReferenceDefinition) {
                $hasDefinitionInside = true;
            }
        });

        return $hasDefinitionInside;
    }
}
