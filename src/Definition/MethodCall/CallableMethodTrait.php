<?php
declare(strict_types=1);

namespace Habemus\Definition\MethodCall;

use Closure;
use Habemus\Definition\Available\ArrayDefinition;
use Psr\Container\ContainerInterface;

trait CallableMethodTrait
{
    /**
     * @var Closure
     */
    protected $methodsCallback;

    public function addMethodCall(string $method, array $parameters = []): self
    {
        $current = $this->getMethodCall();
        $newCallback =
            function ($instance, ContainerInterface $container) use ($current, $method, $parameters) {
                $_parameters = (new ArrayDefinition($parameters))->getConcrete($container);
                $current($instance, $container);
                call_user_func_array([$instance, $method], $_parameters);
            };
        $this->methodsCallback = $newCallback;
        return $this;
    }

    public function getMethodCall(): Closure
    {
        return $this->methodsCallback ?? function ($i, $c) {
        } ;
    }
}
