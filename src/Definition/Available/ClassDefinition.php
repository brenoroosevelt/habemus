<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Habemus\Autowire\ClassResolver;
use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Habemus\Exception\ContainerException;
use Psr\Container\ContainerInterface;
use RuntimeException;

class ClassDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use IdentifiableTrait;
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

    /**
     * @var ClassResolver|null
     */
    protected $classResolver;

    public function __construct(string $class, array $constructor = [])
    {
        $this->class = $class;
        $this->constructorParameters = $constructor;
    }

    public function setClassResolver(ClassResolver $classResolver): self
    {
        $this->classResolver = $classResolver;
        return $this;
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
        if ($this->classResolver === null) {
            throw new ContainerException('No ClassResolver implementation has been set.');
        }

        $arguments = (new ArrayDefinition($this->constructorParameters))->getConcrete($container);
        return $this->classResolver->resolveClass($this->class, $arguments);
    }
}
