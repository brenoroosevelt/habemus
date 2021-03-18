<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Build;

use Habemus\Container;
use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Test\TestCase;

class ArrayDefinitionTest extends TestCase
{
    public function testShouldCreateArrayDefinitionWithConstructorDefault()
    {
        $definition = new ArrayDefinition([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $definition->getValues());
        $this->assertFalse($definition->isRecursive());
    }

    public function testShouldSetArrayDefinitionRecursive()
    {
        $definition = new ArrayDefinition([1, 2, 3]);
        $definition->recursive(true);
        $this->assertTrue($definition->isRecursive());
        $definition->recursive(false);
        $this->assertFalse($definition->isRecursive());
    }

    public function testShouldResolveArrayDefinitionWithoutAlias()
    {
        $definition = new ArrayDefinition([1, 2, 3]);
        $values = $definition->getConcrete(new Container());
        $this->assertSame([1, 2, 3], $values);
    }

    public function testShouldResolveArrayDefinitionWithAlias()
    {
        $container = new Container();
        $container->add('id1', 'v1');
        $definition = new ArrayDefinition([1, 2, new ReferenceDefinition('id1')]);
        $values = $definition->getConcrete($container);
        $this->assertSame([1, 2, 'v1'], $values);
    }

    public function testShouldResolveArrayDefinitionWithAliasRecursive()
    {
        $container = new Container();
        $container->add('id1', 'v1');
        $definition = new ArrayDefinition([1, 2, [[new ReferenceDefinition('id1')]]], true);
        $values = $definition->getConcrete($container);
        $this->assertSame([1, 2, [['v1']]], $values);
    }

    public function testShouldNotResolveAliasOnArrayDefinitionNonRecursive()
    {
        $container = new Container();
        $container->add('id1', 'v1');
        $definition = new ArrayDefinition([1, 2, [[$idDefinition = new ReferenceDefinition('id1')]]], false);
        $values = $definition->getConcrete($container);
        $this->assertSame([1, 2, [[$idDefinition]]], $values);
    }
}
