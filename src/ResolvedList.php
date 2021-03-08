<?php
declare(strict_types=1);

namespace Habemus;

use Countable;
use IteratorAggregate;
use Habemus\Util\Lists\KeyValueList;

class ResolvedList implements IteratorAggregate, Countable
{
    use KeyValueList {
        set as private;
    }

    public function share(string $id, $resolved): void
    {
        $this->set($id, $resolved);
    }
}
