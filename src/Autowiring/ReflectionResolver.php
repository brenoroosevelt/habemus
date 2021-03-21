<?php
declare(strict_types=1);

namespace Habemus\Autowiring;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Autowiring\Parameter\DefaultValueParameterResolver;
use Habemus\Autowiring\Parameter\InjectionParameterResolver;
use Habemus\Autowiring\Parameter\NullableParameterResolver;
use Habemus\Autowiring\Parameter\OptionalParameterResolver;
use Habemus\Autowiring\Parameter\ParameterResolver;
use Habemus\Autowiring\Parameter\TypeHintParameterResolver;
use Habemus\Autowiring\Parameter\UserDefinedParameterResolver;
use Habemus\Container;
use Habemus\Exception\NotFoundException;
use Habemus\Exception\NotInstantiableException;
use Habemus\Exception\UnresolvableParameterException;
use ReflectionClass;
use ReflectionFunctionAbstract;

class ReflectionResolver implements ClassResolver, ArgumentResolver
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

    /**
     * @var ParameterResolver[]
     */
    protected $parameterResolverChain;

    public function __construct(Container $container, AttributesInjection $injection, Reflector $reflector)
    {
        $this->container = $container;
        $this->injection = $injection;
        $this->reflector = $reflector;

        $this->parameterResolverChain = [
            new UserDefinedParameterResolver(),
            new InjectionParameterResolver($this->container, $this->injection),
            new DefaultValueParameterResolver(),
            new NullableParameterResolver(),
            new OptionalParameterResolver(),
            new TypeHintParameterResolver($this->container, $this->reflector)
        ];
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
                $this->resolveArguments($constructor, $constructorArguments)
            );
    }

    /**
     * @inheritDoc
     */
    public function resolveArguments(ReflectionFunctionAbstract $function, array $arguments = []): array
    {
        $resolved = [];
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            foreach ($this->parameterResolverChain as $resolver) {
                $resolver->resolve($parameter, $arguments, $resolved, $params);
            }

            $name = $parameter->getName();
            if (!array_key_exists($name, $resolved)) {
                throw UnresolvableParameterException::createForFunction($function, $name);
            }
        }

        return $params;
    }

    /**
     * @inheritDoc
     */
    public function canResolve(string $className): bool
    {
        return class_exists($className);
    }
}
