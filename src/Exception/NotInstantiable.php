<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

class NotInstantiable extends RuntimeException implements ContainerExceptionInterface
{
    public function __construct(string $className, $message = "", $code = 0, Throwable $previous = null)
    {
        $message = $message ? $message : sprintf("Class (%s) is not instantiable.", $className);
        parent::__construct($message, $code, $previous);
    }
}
