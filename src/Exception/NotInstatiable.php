<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

class NotInstatiable extends RuntimeException implements ContainerExceptionInterface
{
    /**
     * @var string
     */
    protected $className;

    public function __construct(string $className, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->className = $className;
        parent::__construct($message, $code, $previous);
    }

    public static function classCannotBeInstantiated(string $class): self
    {
        return new static($class, sprintf("Class (%s) is not instantiable.", $class));
    }

    public static function classDoesNotExist(string $class): self
    {
        return new static($class, sprintf("Class (%s) does not exist.", $class));
    }

    public function className(): string
    {
        return $this->className;
    }
}
