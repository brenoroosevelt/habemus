<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Container;
use Habemus\Definition\Available\RawDefinition;
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
    public function resolve(string $id, Definition $definition)
    {
        $instance = $definition->getConcrete($this->container);

        if ($definition instanceof CallableMethod) {
            ($definition->getMethodCall())($instance, $this->container);
        }

        if ($this->shouldShare($definition)) {
            $this->resolved->share($id, $instance);
        }

        if ($this->shouldInjectPropertyDependencies($instance, $definition)) {
            $this->attributesInjection->inject($instance);
        }

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function resolveMany(array $definitions = []): array
    {
        $filteredDefnitions
            = array_filter(
                $definitions,
                function ($definition) {
                    return $definition instanceof Definition;
                }
            );

        return array_map(
            function (Definition $definition, $id) {
                return $this->resolve($id, $definition);
            },
            $filteredDefnitions,
            array_keys($filteredDefnitions)
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
            ($definition instanceof Shareable && $definition->isShared());
    }

    /**
     * @param $instance
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
