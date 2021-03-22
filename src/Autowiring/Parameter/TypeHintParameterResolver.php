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
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        $typeHint = $this->reflector->getTypeHint($parameter, false);
        if (!is_string($typeHint) || !$this->container->has($typeHint)) {
            return false;
        }

        $result[] = $this->container->get($typeHint);
        return true;
    }
}
