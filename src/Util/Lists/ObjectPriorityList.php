<?php
declare(strict_types=1);

namespace Habemus\Util\Lists;

use Countable;
use Habemus\Util\PHPVersion;
use InvalidArgumentException;
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
        $this->assertObject($object);
        $this->_set($this->objectID($object), $object, $priority);
    }

    public function delete($object): void
    {
        $this->assertObject($object);
        $this->_delete($this->objectID($object));
    }

    public function has($object): bool
    {
        $this->assertObject($object);
        return $this->_has($this->objectID($object));
    }

    private function objectID($object)
    {
        return PHPVersion::current() >= PHPVersion::V7_2 ? spl_object_id($object) : spl_object_hash($object);
    }

    private function assertObject($object): void
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException(
                sprintf("Expected object. Got: %s", gettype($object))
            );
        }
    }
}
