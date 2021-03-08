<?php
declare(strict_types=1);

namespace Habemus\Util\Lists;

use Countable;
use Habemus\Util\Lists\KeyValuePriorityList;
use IteratorAggregate;

class ObjectPriorityList implements IteratorAggregate, Countable
{
    use KeyValuePriorityList {
        has as private _has;
        get as private _get;
        set as private _set;
        delete as private _delete;
    }

    public function add($object, int $priority): void
    {
        $this->_set($this->objectID($object), $object, $priority);
    }

    public function delete($object): void
    {
        $this->_delete($this->objectID($object));
    }

    public function has($object): bool
    {
        return $this->_has($this->objectID($object));
    }

    private function objectID($object)
    {
        return PHP_VERSION_ID >= 702000 ? spl_object_id($object) : spl_object_hash($object);
    }
}
