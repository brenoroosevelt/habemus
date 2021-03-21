<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Build\ClassDefinition;
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
        if (!$this->acceptConstructorParameters()) {
            throw DefinitionException::unavailableConstructorParameters($this->definition);
        }

        $this->definition->constructor($param, $value);
        return $this;
    }

    public function setShared(bool $share): self
    {
        if (!$this->isShareable()) {
            throw DefinitionException::unshareable($this->definition);
        }

        $this->definition->setShared($share);
        return $this;
    }

    public function isShared(): ?bool
    {
        if ($this->isShareable()) {
            return $this->definition->isShared();
        }

        return null;
    }

    public function addMethodCall(string $method, array $parameters = []): self
    {
        if (!$this->isCallableMethod()) {
            throw DefinitionException::unavailableMethodCall($this->definition);
        }

        $this->definition->addMethodCall($method, $parameters);
        return $this;
    }

    public function addTag(string ...$tag): self
    {
        if (!$this->isTaggable()) {
            throw DefinitionException::untaggable($this->definition);
        }

        foreach ($tag as $_tag) {
            $this->definition->addTag($_tag);
        }

        return $this;
    }

    public function isCallableMethod(): bool
    {
        return $this->definition instanceof CallableMethod;
    }

    public function isTaggable(): bool
    {
        return $this->definition instanceof Taggable;
    }

    public function isShareable(): bool
    {
        return $this->definition instanceof Shareable;
    }

    public function acceptConstructorParameters(): bool
    {
        return $this->definition instanceof ClassDefinition;
    }
}
