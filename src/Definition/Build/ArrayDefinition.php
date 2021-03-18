<?php
declare(strict_types=1);

namespace Habemus\Definition\Build;

use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;

class ArrayDefinition implements Definition, Shareable, Taggable
{
    use IdentifiableTrait;
    use ShareableTrait;
    use TaggableTrait;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var bool
     */
    protected $recursive;

    public function __construct(array $values, bool $recursive = false)
    {
        $this->values = $values;
        $this->recursive = $recursive;
    }

    public function recursive(bool $recursive): self
    {
        $this->recursive = $recursive;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

    public function getConcrete(ContainerInterface $container = null): array
    {
        $values = $this->values;

        if ($this->recursive) {
            array_walk_recursive($values, function (&$item) use ($container) {
                $item = $item instanceof ReferenceDefinition ? $container->get($item->id()) : $item;
            });
        } else {
            array_walk($values, function (&$item) use ($container) {
                $item = $item instanceof ReferenceDefinition ? $container->get($item->id()) : $item;
            });
        }

        return $values;
    }
}
