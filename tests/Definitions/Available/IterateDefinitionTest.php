<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\IterateDefinition;
use Habemus\Exception\NotFound;
use Habemus\Test\TestCase;

class IterateDefinitionTest extends TestCase
{
    public function testShouldCreateIterateDefinition()
    {
        $definition = new IterateDefinition('id1', 'id2');
        $this->assertEquals(['id1', 'id2'], $definition->ids());
    }

    public function testShouldIterateResultFromIterateDefinition()
    {
        $container = new Container();
        $container->add('id1', 10);
        $container->add('id2', 20);
        $container->add('id3', 30);
        $definition = new IterateDefinition('id1', 'id2', 'id3');
        foreach ($definition->getConcrete($container) as $id => $item) {
            $this->assertTrue(in_array($item, [10, 20, 30]));
            $this->assertTrue(in_array($id, ['id1', 'id2', 'id3']));
        }
    }

    public function testShouldIterateDefinitionGetErrorIfNotExists()
    {
        $container = new Container();
        $container->add('id1', 10);
        $container->add('id2', 20);
        $definition = new IterateDefinition('id1', 'id2', 'id3');
        $this->expectException(NotFound::class);
        foreach ($definition->getConcrete($container) as $item) {
        }
    }
}
