<?php
declare(strict_types=1);

namespace Habemus\Utility\Lists;

use Generator;
use LogicException;

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
        $this->setId($id, $value, $priority);
    }

    public function get($id)
    {
        return $this->getId($id);
    }

    public function has($id): bool
    {
        return $this->hasId($id);
    }

    public function delete($id): void
    {
        $this->deleteId($id);
    }

    private function getId($id)
    {
        foreach ($this->elements as $priority) {
            if (array_key_exists($id, $priority)) {
                return $priority[$id];
            }
        }

        throw new LogicException(
            sprintf("Element (%s) not found in list (%s).", $id, get_class($this))
        );
    }

    private function setId($id, $value, int $priority = 0): void
    {
        $this->deleteId($id);
        if (!array_key_exists($priority, $this->elements)) {
            $this->elements[$priority] = [];
        }

        $this->elements[$priority][$id] = $value;
        ksort($this->elements);
    }

    private function hasId($id)
    {
        foreach ($this->elements as $priority) {
            if (array_key_exists($id, $priority)) {
                return true;
            }
        }

        return false;
    }

    private function deleteId($id): void
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

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function getLowestPriority(): ?int
    {
        if (empty($this->elements)) {
            return null;
        }
        return max(array_keys(array_filter($this->elements)));
    }

    public function getHighestPriority(): ?int
    {
        if (empty($this->elements)) {
            return null;
        }
        return min(array_keys(array_filter($this->elements)));
    }
}
