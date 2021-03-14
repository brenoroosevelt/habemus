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

    public function __construct(ContainerInterface $container, Reflector $reflector)
    {
        $this->reflector = $reflector;
        $this->container = $container;
    }

    public function injectProperties($object)
    {
        if (!is_object($object)) {
            throw new \LogicException(
                sprintf("Cannot inject dependencies. Expected object. Got: %s", gettype($object))
            );
        }

        $this->reflector->assertAttributesAvailable();
        $reflectionClass = new ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $property) {
            if (! $injection = $this->getInjection($property)) {
                continue;
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
        $this->reflector->assertAttributesAvailable();
        /** @var Inject|null $inject */
        $inject = $this->reflector->getFirstAttribute($subject, Inject::class);
        if ($inject === null) {
            return null;
        }

        if ($inject->getId() !== null) {
            return $inject->getId();
        }

        return $this->reflector->getTypeHint($subject, false);
    }
}
