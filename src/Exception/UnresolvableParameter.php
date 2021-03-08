<?php
declare(strict_types=1);

namespace Habemus\Exception;

use Psr\Container\ContainerExceptionInterface;
use Throwable;

class UnresolvableParameter extends \RuntimeException implements ContainerExceptionInterface
{
    /**
     * @var string
     */
    protected $parameter;

    public function __construct(string $parameter, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->parameter = $parameter;
        parent::__construct($message, $code, $previous);
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public static function createForFunction(\ReflectionFunctionAbstract $function, string $parameter) : self
    {
        if ($function instanceof \ReflectionMethod) {
            return new self(
                $parameter,
                sprintf(
                    "Container cannot resolve parameter (%s) in (%s::%s)",
                    $parameter,
                    $function->getDeclaringClass(),
                    $function->getName()
                )
            );
        }

        return new self(
            $parameter,
            sprintf("Container cannot resolve parameter (%s)", $parameter)
        );
    }
}
