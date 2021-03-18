<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Build;

use Habemus\Container;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Exception\NotFoundException;
use Habemus\Test\TestCase;
use Psr\Container\ContainerInterface;

class ReferenceDefinitionTest extends TestCase
{
    public function testShouldCreateReferenceDefinition()
    {
        $definition = new ReferenceDefinition('id1');
        $this->assertEquals('id1', $definition->id());
    }

    public function testShouldGetIdDefinitionAsString()
    {
        $definition = new ReferenceDefinition('id1');
        $this->assertEquals('id1', (string) $definition);
    }

    public function testShouldResolveReferenceDefinitionWithContainer()
    {
        $container = new Container();
        $container->add('id1', 10);
        $definition = new ReferenceDefinition('id1');
        $this->assertEquals(10, $definition->getConcrete($container));
    }

    public function testShouldGetErrorIfReferredDefinitionNotExists()
    {
        $definition = new ReferenceDefinition('id1');
        $this->expectException(NotFoundException::class);
        $definition->getConcrete(new Container());
    }

    public function testShouldCallbackFunctionParameterAnInstanceOfContainerPsr11()
    {
        $container = new Container();
        $container->add("id", 1);
        $definition = new ReferenceDefinition('id', function ($instance, $container) {
            $this->assertInstanceOf(ContainerInterface::class, $container);
        });
        $definition->getConcrete($container);
    }

    public function testShouldCallbackDefinitionResolveCallback()
    {
        $container = new Container();
        $container->add("id", 10);
        $definition = new ReferenceDefinition('id', function ($value, $container) {
            return $value + 20;
        });
        $result = $definition->getConcrete($container);
        $this->assertEquals(30, $result);
    }
}
