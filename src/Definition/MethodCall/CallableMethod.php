<?php
declare(strict_types=1);

namespace Habemus\Definition\MethodCall;

use Closure;

interface CallableMethod
{
    /**
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function addMethodCall(string $method, array $parameters = []);

    /**
     * Returns an anonymous function "fn($instance, ContainerInterface $container): void"
     * @return Closure
     */
    public function getMethodCall(): Closure;
}
