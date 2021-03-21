<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Exception\UnresolvableParameterException;
use ReflectionParameter;

class InjectionParameterResolver implements ParameterResolver
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var AttributesInjection
     */
    protected $injection;

    public function __construct(Container $container, AttributesInjection $injection)
    {
        $this->container = $container;
        $this->injection = $injection;
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

        if (!$this->container->attributesEnabled()) {
            return;
        }

        $inject = $this->injection->getInjection($parameter);
        if (empty($inject)) {
            return;
        }

        if (!$this->container->has($inject)) {
            throw UnresolvableParameterException::createForFunction($parameter->getDeclaringFunction(), $name);
        }

        $resolved[$name] = true;
        $result[] = $this->container->get($inject);
    }
}
