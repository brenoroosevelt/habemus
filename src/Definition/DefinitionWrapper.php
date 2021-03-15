<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Tag\Taggable;
use Habemus\Exception\DefinitionException;

final class DefinitionWrapper
{
    /**
     * @var Definition
     */
    protected $definition;

    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function constructor(string $param, $value): self
    {
        if (! $this->definition instanceof ClassDefinition) {
            throw DefinitionException::unavailableConstructorParameters($this->definition);
        }

        $this->definition->constructor($param, $value);
        return $this;
    }

    public function shared(bool $share): self
    {
        if (! $this->definition instanceof Shareable) {
            throw DefinitionException::unshareable($this->definition);
        }

        $this->definition->setShared($share);
        return $this;
    }

    public function addMethodCall(string $method, array $parameters = []): self
    {
        if (! $this->definition instanceof CallableMethod) {
            throw DefinitionException::unavailableMethodCall($this->definition);
        }

        $this->definition->addMethodCall($method, $parameters);
        return $this;
    }

    public function addTag(string ...$tag): self
    {
        if (! $this->definition instanceof Taggable) {
            throw DefinitionException::untaggable($this->definition);
        }

        foreach ($tag as $_tag) {
            $this->definition->addTag($_tag);
        }

        return $this;
    }
}
