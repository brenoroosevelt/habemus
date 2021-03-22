<?php
declare(strict_types=1);

namespace Habemus\Autowiring;

use Habemus\Autowiring\Parameter\ParameterResolver;
use Habemus\Exception\NotFoundException;
use Habemus\Exception\NotInstantiableException;
use Habemus\Exception\UnresolvableParameterException;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;

class ReflectionClassResolver implements ClassResolver
{
    /**
     * @var ParameterResolver
     */
    protected $parameterResolver;

    public function __construct(ParameterResolver $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @inheritDoc
     */
    public function resolveClass(string $className, array $constructorArguments = [])
    {
        if (!$this->canResolve($className)) {
            throw NotFoundException::classNotFound($className);
        }

        $class = new ReflectionClass($className);
        if (!$class->isInstantiable()) {
            throw new NotInstantiableException($className);
        }

        $constructor = $class->getConstructor();
        if ($constructor instanceof ReflectionMethod) {
            return
                $class->newInstanceArgs(
                    $this->resolveArguments($constructor, $constructorArguments)
                );
        }

        return new $className();
    }

    public function resolveArguments(ReflectionFunctionAbstract $function, array $arguments = []): array
    {
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            if (!$this->parameterResolver->resolve($parameter, $arguments, $params)) {
                throw UnresolvableParameterException::createForFunction($function, $parameter->getName());
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
