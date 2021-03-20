<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    /**
     * Array with id of the executed processes until the error happens
     * @var array
     */
    protected $executionStack;

    public static function forId($id, array $executionStack): self
    {
        $error = new self(sprintf("Circular dependency detected for id (%s).", $id));
        $error->executionStack = $executionStack;
        return $error;
    }

    public function getExecutionStack(): array
    {
        return $this->executionStack;
    }
}
