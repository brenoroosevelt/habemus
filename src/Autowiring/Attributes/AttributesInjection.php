<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Attributes;

use Exception;
use Habemus\Autowiring\Reflector;
use Habemus\Exception\InjectionException;
use LogicException;
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

    public function inject($object)
    {
        if (!is_object($object)) {
            throw InjectionException::notAnObject($object);
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

            if (!$this->container->has($injection)) {
                throw InjectionException::unresolvablePropertyInjection($property, $object);
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

        $typeHint = $this->reflector->getTypeHint($subject, false);
        if ($typeHint === null) {
            throw InjectionException::invalidInjection($subject);
        }

        return $typeHint;
    }
}
