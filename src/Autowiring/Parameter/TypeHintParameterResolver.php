<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use Habemus\Autowiring\Reflector;
use Psr\Container\ContainerInterface;
use ReflectionParameter;

class TypeHintParameterResolver implements ParameterResolver
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
        $this->container = $container;
        $this->reflector = $reflector;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        $typeHint = $this->reflector->getTypeHint($parameter, false);
        if (!is_string($typeHint) || !$this->container->has($typeHint)) {
            return false;
        }

        $value = $this->container->get($typeHint);
        return (new VariadicParameterResolver($value))->resolve($parameter, $arguments, $result);
    }
}
