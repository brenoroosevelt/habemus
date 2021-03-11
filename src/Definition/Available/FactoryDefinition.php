<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Habemus\Definition\Definition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;

class FactoryDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /** @var string|object|IdDefinition */
    protected $classOrObject;

    /** @var string|object */
    protected $factory;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $methodParams;

    /**
     * @var bool
     */
    protected $staticCall;

    public function __construct(
        $classOrObject,
        string $method = "__invoke",
        array $methodParams = [],
        bool $static = false
    ) {
        $this->classOrObject = $classOrObject;
        $this->method = $method;
        $this->methodParams = $methodParams;
        $this->factory = null;
        $this->staticCall = $static;
        $this->shared = false;
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

    public function getClassOrObject()
    {
        return $this->classOrObject;
    }

    public function isStaticCall(): bool
    {
        return $this->staticCall;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getMethodParams(): array
    {
        return $this->methodParams;
    }

    public function getConcrete(ContainerInterface $container)
    {
        $this->factory = $this->factoryInstance($container);

        if (!method_exists($this->factory, $this->method)) {
            throw new \InvalidArgumentException("invalid");
        }

        return call_user_func_array([$this->factory, $this->method], $this->resolveParams($container));
    }

    protected function factoryInstance(ContainerInterface $container)
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        if ($this->classOrObject instanceof IdDefinition) {
            return $this->factory = $container->get($this->classOrObject->id());
        }

        if ($this->staticCall) {
            return $this->factory = $this->classOrObject;
        }

        if (!is_object($this->classOrObject) && is_string($this->classOrObject)) {
            return $this->factory = $container->get($this->classOrObject);
        }

        return $this->factory = $this->classOrObject;
    }

    protected function resolveParams(ContainerInterface $container): array
    {
        return (new ArrayDefinition($this->methodParams))->getConcrete($container);
    }
}
