<?php
declare(strict_types=1);

namespace Habemus\Utility\Lists;

use Generator;
use Habemus\Exception\NotFoundException;
use LogicException;

trait KeyValueList
{
    /**
     * @var array
     */
    protected $elements = [];

    public function set($id, $value): void
    {
        $this->elements[$id] = $value;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw NotFoundException::noEntryWasFound((string) $id);
        }

        return $this->elements[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->elements);
    }

    public function delete($id): void
    {
        unset($this->elements[$id]);
    }

    public function getIterator(): Generator
    {
        foreach ($this->elements as $id => $element) {
            yield $id => $element;
        }
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }
}
