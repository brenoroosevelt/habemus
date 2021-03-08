<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Habemus\Definition\Definition;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;
use Traversable;

class IterateDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /**
     * @var string[]
     */
    protected $ids;

    public function __construct(string ...$id)
    {
        $this->ids = $id;
    }

    public function getConcrete(ContainerInterface $container): Traversable
    {
        foreach ($this->ids as $id) {
            yield $container->get($id);
        }
    }
}
