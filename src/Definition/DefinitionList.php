<?php
declare(strict_types=1);

namespace Habemus\Definition;

use Habemus\Definition\Tag\Taggable;
use Habemus\Util\Lists\KeyValueList;
use IteratorAggregate;

/**
 * @method Definition get($id):
 */
class DefinitionList implements IteratorAggregate
{
    use KeyValueList {
        set as private;
    }

    public function add(string $id, Definition $definition)
    {
        $this->set($id, $definition);
    }

    public function hasTag(string $tag): bool
    {
        foreach ($this->elements as $definition) {
            if ($definition instanceof Taggable && $definition->hasTag($tag)) {
                return true;
            }
        }

        return false;
    }

    public function getTagged(string $tag): array
    {
        return array_filter(
            $this->elements,
            function (Definition $definition) use ($tag) {
                return $definition instanceof Taggable && $definition->hasTag($tag);
            }
        );
    }
}
