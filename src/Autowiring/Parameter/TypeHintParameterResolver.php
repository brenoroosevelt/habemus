<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use Habemus\Autowiring\Reflector;
use Habemus\Container;
use ReflectionParameter;

class TypeHintParameterResolver implements ParameterResolver
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Reflector
     */
    protected $reflector;

    public function __construct(Container $container, Reflector $reflector)
    {
        $this->container = $container;
        $this->reflector = $reflector;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$resolved, array &$result): void
    {
        $name = $parameter->getName();
        if (array_key_exists($name, $resolved)) {
            return;
        }

        $typeHint = $this->reflector->getTypeHint($parameter, false);
        if (!is_string($typeHint) || !$this->container->has($typeHint)) {
            return;
        }

        $resolved[$name] = true;
        $result[] = $this->container->get($typeHint);
    }
}
