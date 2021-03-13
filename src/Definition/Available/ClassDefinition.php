<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Habemus\Container;
use Habemus\Definition\Definition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;
use RuntimeException;

class ClassDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $constructorParameters;

    public function __construct(string $class, array $constructor = [])
    {
        $this->class = $class;
        $this->constructorParameters = $constructor;
    }

    public function constructor(string $param, $value): self
    {
        $this->constructorParameters[$param] = $value;
        return $this;
    }

    public function getConstructorParameters(): array
    {
        return $this->constructorParameters;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function getConcrete(ContainerInterface $container)
    {
        if (! $container instanceof Container) {
            throw new RuntimeException('Invalid container.');
        }

        $arguments = (new ArrayDefinition($this->constructorParameters))->getConcrete($container);
        return $container->getClassResolver()->resolveClass($this->class, $arguments);
    }
}
