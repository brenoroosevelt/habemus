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

class IdDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /**
     * @var string
     */
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function getConcrete(ContainerInterface $container)
    {
        return $container->get($this->id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
