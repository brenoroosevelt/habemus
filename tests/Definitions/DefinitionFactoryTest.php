<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Definition\Available\ArrayDefinition;
use Habemus\Definition\Available\CallbackDefinition;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FactoryDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\IdsDefinition;
use Habemus\Definition\Available\IterateDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionFactory;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use RuntimeException;

class DefinitionFactoryTest extends TestCase
{
    public function testShouldFactoryCreateRawDefinition()
    {
        $definition = DefinitionFactory::raw(1);
        $this->assertInstanceOf(RawDefinition::class, $definition);
    }

    public function testShouldFactoryCreateArrayDefinition()
    {
        $definition = DefinitionFactory::array([1, 2, 3]);
        $this->assertInstanceOf(ArrayDefinition::class, $definition);
    }

    public function testShouldFactoryCreateCallbackDefinition()
    {
        $definition = DefinitionFactory::callback('id', function () {
        });
        $this->assertInstanceOf(CallbackDefinition::class, $definition);
    }

    public function testShouldFactoryCreateFnDefinition()
    {
        $definition = DefinitionFactory::fn(function () {
        });
        $this->assertInstanceOf(FnDefinition::class, $definition);
    }

    public function testShouldFactoryCreateClassDefinition()
    {
        $definition = DefinitionFactory::class(ClassA::class);
        $this->assertInstanceOf(ClassDefinition::class, $definition);
    }

    public function testShouldFactoryCreateFactoryDefinition()
    {
        $definition = DefinitionFactory::factory('class', 'method');
        $this->assertInstanceOf(FactoryDefinition::class, $definition);
    }

    public function testShouldFactoryCreateIterateDefinition()
    {
        $definition = DefinitionFactory::iterate('id1', 'id2');
        $this->assertInstanceOf(IterateDefinition::class, $definition);
    }

    public function testShouldFactoryCreateIdsDefinition()
    {
        $definition = DefinitionFactory::ids('id1', 'id2');
        $this->assertInstanceOf(IdsDefinition::class, $definition);
    }

    public function testShouldGetErrorUndefinedDefinition()
    {
        $this->expectException(RuntimeException::class);
        DefinitionFactory::undefined('id1', 'id2');
    }
}
