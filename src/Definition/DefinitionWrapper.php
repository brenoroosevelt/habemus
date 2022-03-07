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

    /**
     * @throws DefinitionException
     */
    public function constructor(string $param, $value): self
    {
        if ($this->definition instanceof ClassDefinition) {
            $this->definition->constructor($param, $value);

            return $this;
        }

        throw DefinitionException::unavailableConstructorParameters($this->definition);
    }

    /**
     * @throws DefinitionException
     */
    public function setShared(bool $share): self
    {
        if ($this->definition instanceof Shareable) {
            $this->definition->setShared($share);

            return $this;
        }

        throw DefinitionException::unshareable($this->definition);
    }

    public function isShared(): ?bool
    {
        if ($this->definition instanceof Shareable) {
            return $this->definition->isShared();
        }

        return null;
    }

    /**
     * @throws DefinitionException
     */
    public function addMethodCall(string $method, array $parameters = []): self
    {
        if ($this->definition instanceof CallableMethod) {
            $this->definition->addMethodCall($method, $parameters);

            return $this;
        }

        throw DefinitionException::unavailableMethodCall($this->definition);
    }

    /**
     * @throws DefinitionException
     */
    public function addTag(string ...$tag): self
    {
        if ($this->definition instanceof Taggable) {
            foreach ($tag as $_tag) {
                $this->definition->addTag($_tag);
            }

            return $this;
        }

        throw DefinitionException::untaggable($this->definition);
    }
}
