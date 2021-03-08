<?php
declare(strict_types=1);

namespace Habemus\Definition\MethodCall;

use Closure;

interface CallableMethod
{
    public function addMethodCall(string $method, array $parameters = []): self;
    public function getMethodCall(): Closure;
}
