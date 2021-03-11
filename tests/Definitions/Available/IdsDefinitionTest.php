<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\IdsDefinition;
use Habemus\Exception\NotFound;
use Habemus\Test\TestCase;

class IdsDefinitionTest extends TestCase
{
    public function testShouldCreateIdsDefinition()
    {
        $definition = new IdsDefinition('id1', 'id2');
        $this->assertEquals(['id1', 'id2'], $definition->ids());
    }

    public function testShouldResolveIdsDefinitionWithContainer()
    {
        $container = new Container();
        $container->add('id1', 10);
        $container->add('id2', 20);
        $definition = new IdsDefinition('id1', 'id2');
        $this->assertEquals([10, 20], $definition->getConcrete($container));
    }

    public function testShouldGetErrorIfAnyIdNotExists()
    {
        $container = new Container();
        $container->add('id1', 10);
        $definition = new IdsDefinition('id1', 'id2');
        $this->expectException(NotFound::class);
        $definition->getConcrete($container);
    }
}
