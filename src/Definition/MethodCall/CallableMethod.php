<?php
declare(strict_types=1);

namespace Habemus\Definition\MethodCall;

use Closure;

interface CallableMethod
{
    /**
     * @param string $method
     * @param array $parameters
     * @return self
     */
    public function addMethodCall(string $method, array $parameters = []);
    public function getMethodCall(): Closure;
}
