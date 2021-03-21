<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Attributes;

use Habemus\Autowiring\Reflector;
use Habemus\Exception\ContainerException;
use Habemus\Exception\InjectionException;
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

        $reflectionClass = new ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $property) {
            $injection = $this->getInjection($property);
            if (empty($injection)) {
                continue;
            }

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            if (!$this->container->has($injection)) {
                throw InjectionException::unresolvablePropertyInjection($property, $object);
            }

            $value = $this->container->get($injection);
            $property->setValue($object, $value);
        }
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $subject
     * @return string|null
     * @throws InjectionException|ContainerException
     */
    public function getInjection($subject): ?string
    {
        /** @var Inject|null $inject */
        $inject = $this->reflector->getFirstAttribute($subject, Inject::class);
        if (! $inject instanceof Inject) {
            return null;
        }

        if (!empty($inject->getId())) {
            return $inject->getId();
        }

        $typeHint = $this->reflector->getTypeHint($subject, false);
        if (empty($typeHint)) {
            throw InjectionException::invalidInjection($subject);
        }

        return $typeHint;
    }
}
