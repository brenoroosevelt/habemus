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
    protected $class;

    /** @var string|object */
    protected $factory;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var bool
     */
    protected $staticCall;

    public function __construct($class, string $method, array $params = [], bool $static = false)
    {
        $this->class = $class;
        $this->method = $method;
        $this->params = $params;
        $this->factory = null;
        $this->staticCall = $static;
        $this->shared = false;
    }

    public function addParam($param): self
    {
        $this->params[] = $param;
        return $this;
    }

    public function staticCall(bool $static): self
    {
        $this->staticCall = $static;
        return $this;
    }

    public function getConcrete(ContainerInterface $container)
    {
        $this->factory = $this->factoryInstance($container);
        $params = (new ArrayDefinition($this->params))->getConcrete($container);
        return call_user_func_array([$this->factory, $this->method], $params);
    }

    protected function factoryInstance(ContainerInterface $container)
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        if ($this->class instanceof IdDefinition) {
            return $container->get($this->class->id());
        }

        if ($this->staticCall) {
            return $this->class;
        }

        if (!is_object($this->class) && is_string($this->class)) {
            return $container->get($this->class);
        }

        return $this->class;
    }
}
