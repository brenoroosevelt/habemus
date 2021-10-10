<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Exception\UnresolvableParameterException;
use Psr\Container\ContainerInterface;
use ReflectionParameter;

class InjectionParameterResolver implements ParameterResolver
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var AttributesInjection
     */
    protected $injection;

    public function __construct(ContainerInterface $container, AttributesInjection $injection)
    {
        $this->container = $container;
        $this->injection = $injection;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        if ($this->container instanceof Container && !$this->container->attributesEnabled()) {
            return false;
        }

        $inject = $this->injection->getInjection($parameter);
        if (empty($inject)) {
            return false;
        }

        if (!$this->container->has($inject)) {
            throw UnresolvableParameterException::createForFunction(
                $parameter->getDeclaringFunction(),
                $parameter->getName()
            );
        }

        $value = $this->container->get($inject);
        return (new VariadicParameterResolver($value))->resolve($parameter, $arguments, $result);
    }
}
