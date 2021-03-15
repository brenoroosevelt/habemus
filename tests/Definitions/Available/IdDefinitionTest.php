<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Exception\NotFoundException;
use Habemus\Test\TestCase;

class IdDefinitionTest extends TestCase
{
    public function testShouldCreateIdDefinition()
    {
        $definition = new IdDefinition('id1');
        $this->assertEquals('id1', $definition->id());
    }

    public function testShouldGetIdDefinitionAsString()
    {
        $definition = new IdDefinition('id1');
        $this->assertEquals('id1', (string) $definition);
    }

    public function testShouldResolveIdDefinitionWithContainer()
    {
        $container = new Container();
        $container->add('id1', 10);
        $definition = new IdDefinition('id1');
        $this->assertEquals(10, $definition->getConcrete($container));
    }

    public function testShouldGetErrorIfIdDefinitionNotExists()
    {
        $definition = new IdDefinition('id1');
        $this->expectException(NotFoundException::class);
        $definition->getConcrete(new Container());
    }
}
