<?php
declare(strict_types=1);

namespace Habemus;

use Closure;

class CircularDependencyDetection
{
    /**
     * @var array
     */
    protected $executing = [];

    public function execute(string $id, Closure $process)
    {
        if ($this->isExecuting($id)) {
            throw new \RuntimeException(sprintf("Circular dependency detected for id (%s).", $id));
        }

        $this->acquire($id);

        try {
            return $process();
        } finally {
            $this->release($id);
        }
    }

    protected function acquire($id): void
    {
        $this->executing[$id] = true;
    }

    protected function release($id): void
    {
        unset($this->executing[$id]);
    }

    protected function isExecuting($id): bool
    {
        return array_key_exists($id, $this->executing);
    }
}
