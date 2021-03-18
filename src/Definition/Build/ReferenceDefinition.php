<?php
declare(strict_types=1);

namespace Habemus\Definition\Build;

use Closure;
use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;

class ReferenceDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use IdentifiableTrait;
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Closure
     */
    protected $fn;

    public function __construct(string $id, Closure $fn = null)
    {
        $this->id = $id;
        $this->fn = $fn !== null ? $fn : function ($instance, ContainerInterface $c) {
            return $instance;
        };
    }

    public function id(): string
    {
        return $this->id;
    }

    public function getConcrete(ContainerInterface $container)
    {
        return ($this->fn)($container->get($this->id), $container);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
