<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Closure;
use Habemus\Definition\Definition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;

class CallbackDefinition implements Definition, Shareable, CallableMethod, Taggable
{
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

    public function __construct(string $id, Closure $fn)
    {
        $this->id = $id;
        $this->fn = $fn;
    }

    public function getConcrete(ContainerInterface $container)
    {
        return ($this->fn)($container->get($this->id), $container);
    }
}
