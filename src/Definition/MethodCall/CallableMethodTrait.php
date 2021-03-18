<?php
declare(strict_types=1);

namespace Habemus\Definition\MethodCall;

use Closure;
use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Exception\DefinitionException;
use Psr\Container\ContainerInterface;

trait CallableMethodTrait
{
    /**
     * @var Closure
     */
    protected $methodsCallback;

    public function addMethodCall(string $method, array $parameters = [])
    {
        $current = $this->getMethodCall();
        $newCallback =
            function ($instance, ContainerInterface $container) use ($current, $method, $parameters) {
                $_parameters = (new ArrayDefinition($parameters))->getConcrete($container);
                $current($instance, $container);
                if (!is_object($instance) || !method_exists($instance, $method)) {
                    throw DefinitionException::invalidMethodCall($this, $instance, $method);
                }
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
