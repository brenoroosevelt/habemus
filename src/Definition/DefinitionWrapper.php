<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Definition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Tag\Taggable;

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
        if ($this->definition instanceof ClassDefinition) {
            $this->definition->constructor($param, $value);
        }

        return $this;
    }

    public function shared(bool $share): self
    {
        if ($this->definition instanceof Shareable) {
            $this->definition->setShared($share);
        }

        return $this;
    }

    public function addMethodCall(string $method, array $parameters = []): self
    {
        if ($this->definition instanceof CallableMethod) {
            $this->definition->addMethodCall($method, $parameters);
        }

        return $this;
    }

    public function addTag(string ...$tag): self
    {
        if ($this->definition instanceof Taggable) {
            foreach ($tag as $_tag) {
                $this->definition->addTag($_tag);
            }
        }

        return $this;
    }
}
