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

    public function add(Definition $definition)
    {
        $this->set($definition->getIdentity(), $definition);
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

    /**
     * @param string $tag
     * @return Definition[]
     */
    public function getTagged(string $tag): array
    {
        $tagged = [];
        foreach ($this->elements as $definition) {
            if ($definition instanceof Taggable && $definition->hasTag($tag)) {
                $tagged[] = $definition;
            }
        }

        return $tagged;
    }
}
