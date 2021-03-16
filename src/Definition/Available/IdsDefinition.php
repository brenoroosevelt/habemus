<?php
declare(strict_types=1);

namespace Habemus\Definition\Available;

use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Psr\Container\ContainerInterface;

class IdsDefinition implements Definition, Shareable, Taggable
{
    use IdentifiableTrait;
    use ShareableTrait;
    use TaggableTrait;

    /**
     * @var string[]
     */
    protected $ids;

    public function __construct(string ...$ids)
    {
        $this->ids = $ids;
    }

    public function ids(): array
    {
        return $this->ids;
    }

    public function getConcrete(ContainerInterface $container)
    {
        return array_map(
            function ($id) use ($container) {
                return  $container->get($id);
            },
            $this->ids
        ) ;
    }
}
