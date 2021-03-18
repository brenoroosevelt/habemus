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

class FnDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use IdentifiableTrait;
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /**
     * @var Closure
     */
    protected $fn;

    public function __construct(Closure $fn)
    {
        $this->fn = $fn;
        $this->setShared(false);
    }

    public function getConcrete(ContainerInterface $container)
    {
        return ($this->fn)($container);
    }
}
