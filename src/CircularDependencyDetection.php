<?php
declare(strict_types=1);

namespace Habemus;

use Closure;
use RuntimeException;

class CircularDependencyDetection
{
    /**
     * @var array
     */
    protected $executing = [];

    public function execute($id, Closure $process)
    {
        if ($this->isExecuting($id)) {
            throw new RuntimeException(sprintf("Circular dependency detected for id (%s).", $id));
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

    public function isExecuting($id): bool
    {
        return array_key_exists($id, $this->executing);
    }
}
