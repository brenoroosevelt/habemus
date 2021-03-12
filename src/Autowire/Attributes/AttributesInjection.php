<?php
declare(strict_types=1);

namespace Habemus\Autowire\Attributes;

use Habemus\Autowire\Reflector;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

class AttributesInjection
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Reflector
     */
    protected $reflector;

    public function __construct(ContainerInterface $container, Reflector $reflector = null)
    {
        $this->reflector = $reflector !== null ? $reflector : new Reflector();
        $this->reflector->assertAttributesAvailable();
        $this->container = $container;
    }

    public function injectProperties($object)
    {
        if (!is_object($object)) {
            return;
        }

        $reflectionClass = new ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $property) {
            if (! $injection = $this->getInjection($property)) {
                continue;
            }

            if (!$this->container->has($injection)) {
                continue; // ... or throw error !?
            }

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }
            $instance = $this->container->get($injection);
            $property->setValue($object, $instance);
        }
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $subject
     * @return string|null
     */
    public function getInjection($subject): ?string
    {
        $inject = $this->reflector->getFirstAttribute($subject, Inject::class);
        if ($inject === null) {
            return null;
        }

        if ($inject->id !== null) {
            return $inject->id;
        }

        return $this->reflector->getTypeHint($subject, false);
    }
}
