<?php
declare(strict_types=1);

namespace Habemus\Definition\Build;

use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Habemus\Exception\DefinitionException;
use Psr\Container\ContainerInterface;

class FactoryDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use IdentifiableTrait;
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /** @var string|object|ReferenceDefinition */
    protected $objectOrClass;

    /** @var string|object|null */
    protected $factory;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var array
     */
    protected $methodParams;

    /**
     * @var bool
     */
    protected $staticCall;

    public function __construct(
        $objectOrClass,
        string $methodName = "__invoke",
        array $methodParams = [],
        bool $static = false
    ) {
        $this->objectOrClass = $objectOrClass;
        $this->methodName = $methodName;
        $this->methodParams = $methodParams;
        $this->factory = null;
        $this->staticCall = $static;
        $this->setShared(false);
    }

    public function methodParams(array $params): self
    {
        $this->methodParams = $params;
        return $this;
    }

    public function staticCall(bool $static): self
    {
        $this->staticCall = $static;
        return $this;
    }

    public function getObjectOrClass()
    {
        return $this->objectOrClass;
    }

    public function isStaticCall(): bool
    {
        return $this->staticCall;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getMethodParams(): array
    {
        return $this->methodParams;
    }

    public function getConcrete(ContainerInterface $container)
    {
        $this->factory = $this->factoryInstance($container);

        if (!method_exists($this->factory, $this->methodName)) {
            throw DefinitionException::invalidMethodCall($this, $this->factory, $this->methodName);
        }

        return call_user_func_array([$this->factory, $this->methodName], $this->resolveParams($container));
    }

    protected function factoryInstance(ContainerInterface $container)
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        if ($this->objectOrClass instanceof ReferenceDefinition) {
            return $this->factory = $container->get($this->objectOrClass->id());
        }

        if ($this->staticCall) {
            return $this->factory = $this->objectOrClass;
        }

        if (!is_object($this->objectOrClass) && is_string($this->objectOrClass)) {
            return $this->factory = $container->get($this->objectOrClass);
        }

        return $this->factory = $this->objectOrClass;
    }

    protected function resolveParams(ContainerInterface $container): array
    {
        return (new ArrayDefinition($this->methodParams))->getConcrete($container);
    }
}
