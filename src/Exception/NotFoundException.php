<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public function __construct(string $id, $message = "", $code = 0, Throwable $previous = null)
    {
        $message = $message ? $message : sprintf("No entry was found in the container for id (%s)", $id);
        parent::__construct($message, $code, $previous);
    }

    public static function noEntryWasFound(string $id): self
    {
        return new static($id);
    }

    public static function classNotFound(string $class): self
    {
        return new static(
            $class,
            sprintf("Class not found (%s)", $class)
        );
    }
}
