<?php
declare(strict_types=1);

namespace Habemus\Autowiring\Parameter;

use ReflectionParameter;

class VariadicParameterResolver implements ParameterResolver
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ReflectionParameter $parameter, array $arguments, array &$result): bool
    {
        if (!$parameter->isVariadic()) {
            $result[] = $this->value;
        } else {
            $argument = !is_array($this->value) ? [$this->value] : $this->value;
            $result = array_merge($result, $argument);
        }

        return true;
    }
}
