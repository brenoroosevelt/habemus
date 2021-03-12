<?php
declare(strict_types=1);

namespace Habemus\Util\Lists;

use Generator;

/*
 * The order of elements with identical priority is undefined
 */
trait KeyValuePriorityList
{
    /**
     * Fisrt level ordered by priority. Second level indexed by id.
     * @var array
     */
    private $elements = [];

    /**
     * keeps array ordered by priority after insertion.
     */
    public function set($id, $value, int $priority = 0): void
    {
        if (!array_key_exists($priority, $this->elements)) {
            $this->elements[$priority] = [];
        }

        $this->elements[$priority][$id] = $value;
        ksort($this->elements);
    }

    public function get($id)
    {
        foreach ($this->elements as $priority) {
            if (array_key_exists($id, $priority)) {
                return $priority[$id];
            }
        }

        throw new \LogicException("Element (%s) not found in list (%).", $id, get_class($this));
    }

    public function has($id)
    {
        foreach ($this->elements as $priority) {
            if (array_key_exists($id, $priority)) {
                return true;
            }
        }

        return false;
    }

    public function delete($id): void
    {
        foreach ($this->elements as $priority => $items) {
            if (array_key_exists($id, $items)) {
                unset($this->elements[$priority][$id]);
                break;
            }
        }
    }

    public function getIterator(): Generator
    {
        foreach ($this->elements as $elements) {
            foreach ($elements as $id => $element) {
                yield $id => $element;
            }
        }
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->elements as $elements) {
            $count += count($elements);
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }
}
