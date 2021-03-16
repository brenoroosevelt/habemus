<?php
declare(strict_types=1);

namespace Habemus\Autowire;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Exception\NotFoundException;
use Habemus\Exception\NotInstantiableException;
use Habemus\Exception\UnresolvableParameterException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;

class ReflectionClassResolver implements ClassResolver
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * @var AttributesInjection
     */
    protected $injection;

    public function __construct(Container $container, AttributesInjection $injection, Reflector $reflector)
    {
        $this->container = $container;
        $this->injection = $injection;
        $this->reflector = $reflector;
    }

    /**
     * @inheritDoc
     */
    public function resolveClass(string $className, array $constructorArguments = [])
    {
        if (!class_exists($className)) {
            throw NotFoundException::classNotFound($className);
        }

        $class = new ReflectionClass($className);
        if (!$class->isInstantiable()) {
            throw new NotInstantiableException($className);
        }

        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return new $className();
        }

        return
            $class->newInstanceArgs(
                $this->resolveParameters($constructor, $constructorArguments)
            );
    }

    /**
     * @param ReflectionFunctionAbstract $function
     * @param array $arguments
     * @return array
     * @throws ReflectionException
     */
    public function resolveParameters(ReflectionFunctionAbstract $function, $arguments = []): array
    {
        $result = [];
        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $arguments)) {
                if ($parameter->isVariadic()) {
                    $_argument = !is_array($arguments[$name]) ? [$arguments[$name]] : $arguments[$name];
                    $result = array_merge($result, $_argument);
                } else {
                    $result[] = $arguments[$name];
                }
                continue;
            }

            if ($this->container->attributesEnabled()) {
                $inject = $this->injection->getInjection($parameter);
                if ($inject !== null) {
                    if (!$this->container->has($inject)) {
                        throw UnresolvableParameterException::createForFunction($function, $name);
                    }
                    $result[] = $this->container->get($inject);
                    continue;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $result[] = $parameter->getDefaultValue();
                continue;
            }

            if ($parameter->allowsNull() && !$parameter->isOptional()) {
                $result[] = null;
                continue;
            }

            if ($parameter->isOptional()) {
                continue;
            }

            $typeHint = $this->reflector->getTypeHint($parameter, false);
            if ($typeHint && $this->container->has($typeHint)) {
                $result[] = $this->container->get($typeHint);
                continue;
            }

            throw UnresolvableParameterException::createForFunction($function, $name);
        }

        return $result;
    }

    public function canResolve(string $className): bool
    {
        return class_exists($className);
    }
}
