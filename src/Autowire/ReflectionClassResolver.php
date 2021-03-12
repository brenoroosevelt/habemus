<?php
declare(strict_types=1);

namespace Habemus\Autowire;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Exception\NotFound;
use Habemus\Exception\NotInstatiable;
use Habemus\Exception\UnresolvableParameter;
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

    public function __construct(Container $container, Reflector $reflector = null)
    {
        $this->container = $container;
        $this->reflector = $reflector !== null ? $reflector : new Reflector();
    }

    /**
     * @inheritDoc
     */
    public function resolveClass(string $className, array $constructorArguments = [])
    {
        if (!class_exists($className)) {
            throw NotFound::classNotFound($className);
        }

        $class = new ReflectionClass($className);
        if (!$class->isInstantiable()) {
            throw NotInstatiable::classCannotBeInstantiated($className);
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
    protected function resolveParameters(ReflectionFunctionAbstract $function, $arguments = []): array
    {
        $result = [];
        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();

            // pre-defined arguments
            if (array_key_exists($name, $arguments)) {
                if ($parameter->isVariadic()) {
                    $result = array_merge($result, $arguments[$name]);
                } else {
                    $result[] = $arguments[$name];
                }
                continue;
            }

            // #[Inject(...)]
            if ($this->container->attributesEnabled()) {
                $inject = (new AttributesInjection($this->container))->getInjection($parameter);
                if ($inject && $this->container->has($inject)) {
                    $result[] = $this->container->get($inject);
                    continue;
                }
            }

            // fn($x=1), fn($x=null), fn(?int $x = 1), fn(?int $x = null)
            if ($parameter->isDefaultValueAvailable()) {
                    $result[] = $parameter->getDefaultValue();
                    continue;
            }

            // fn($x, $y),fn(?int $x)
            if ($parameter->allowsNull() && !$parameter->isOptional()) {
                $result[] = null;
                continue;
            }

            // fn($x = null, string ...$y), fn(?int ...$y)
            if ($parameter->isOptional()) {
                continue;
            }

            // fn(User $user)
            $typeHint = $this->reflector->getTypeHint($parameter, false);
            if ($typeHint && $this->container->has($typeHint)) {
                $result[] = $this->container->get($typeHint);
                continue;
            }

            throw UnresolvableParameter::createForFunction($function, $name);
        }

        return $result;
    }
}
