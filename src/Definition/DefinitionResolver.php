<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Definition\Build\RawDefinition;
use Habemus\ResolvedList;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\Sharing\Shareable;

class DefinitionResolver implements DefinitionResolverInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ResolvedList
     */
    protected $resolved;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjection;

    public function __construct(Container $container, ResolvedList $resolved, AttributesInjection $attributesInjection)
    {
        $this->container = $container;
        $this->resolved = $resolved;
        $this->attributesInjection = $attributesInjection;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Definition $definition)
    {
        $instance = $definition->getConcrete($this->container);

        if ($definition instanceof CallableMethod) {
            ($definition->getMethodCall())($instance, $this->container);
        }

        if ($this->shouldShare($definition)) {
            $this->resolved->share($definition->getIdentity(), $instance);
        }

        if ($this->shouldInjectPropertyDependencies($instance, $definition)) {
            $this->attributesInjection->inject($instance);
        }

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function resolveMany(Definition ...$definitions): array
    {
        return array_map(
            function (Definition $definition) {
                return $this->resolve($definition);
            },
            $definitions
        );
    }

    /**
     * @param Definition $definition
     * @return bool
     */
    protected function shouldShare(Definition $definition): bool
    {
        return
            $definition instanceof RawDefinition ||
            ($definition instanceof Shareable && $definition->isShared() && $definition->getIdentity() !== null);
    }

    /**
     * @param mixed $instance
     * @param Definition $definition
     * @return bool
     */
    protected function shouldInjectPropertyDependencies($instance, Definition $definition): bool
    {
        return
            $this->container->attributesEnabled() &&
            is_object($instance) &&
            !($definition instanceof RawDefinition);
    }
}
