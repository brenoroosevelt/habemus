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

class RawDefinition implements Definition, Shareable, CallableMethod, Taggable
{
    use ShareableTrait;
    use CallableMethodTrait;
    use TaggableTrait;

    /** @var mixed */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getConcrete(ContainerInterface $container)
    {
        return $this->value;
    }
}
