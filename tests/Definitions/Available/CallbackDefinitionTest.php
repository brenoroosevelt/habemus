<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\CallbackDefinition;
use Habemus\Test\TestCase;
use Psr\Container\ContainerInterface;

class CallbackDefinitionTest extends TestCase
{
    public function testShouldCreateACallbackDefinition()
    {
        $definition = new CallbackDefinition('id', function ($instance, $container) {
        });
        $this->assertInstanceOf(CallbackDefinition::class, $definition);
    }

    public function testShouldCallbackFunctionParameterAnInstanceOfContainerPsr11()
    {
        $container = new Container();
        $container->add("id", 1);
        $definition = new CallbackDefinition('id', function ($instance, $container) {
            $this->assertInstanceOf(ContainerInterface::class, $container);
        });
        $definition->getConcrete($container);
    }

    public function testShouldCallbackDefinitionResolveCallback()
    {
        $container = new Container();
        $container->add("id", 10);
        $definition = new CallbackDefinition('id', function ($value, $container) {
            return $value + 20;
        });
        $result = $definition->getConcrete($container);
        $this->assertEquals(30, $result);
    }
}
