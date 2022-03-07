<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Definition\Build\ArrayDefinition;
use Habemus\Definition\Build\ClassDefinition;
use Habemus\Definition\Build\FactoryDefinition;
use Habemus\Definition\Build\FnDefinition;
use Habemus\Definition\Build\IterateDefinition;
use Habemus\Definition\Build\RawDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Definition\DefinitionBuilder;
use Habemus\Exception\InvalidDefinitionException;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\FactoryClass;
use Habemus\Test\Stub\ClassUsingDefinitionBuilder;
use Habemus\Test\TestCase;

class DefinitionBuilderTest extends TestCase
{
    public function testShouldFactoryCreateRawDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::raw(1);
        $this->assertInstanceOf(RawDefinition::class, $definition);
    }

    public function testShouldFactoryCreateArrayDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::array([1, 2, 3]);
        $this->assertInstanceOf(ArrayDefinition::class, $definition);
    }

    public function testShouldFactoryCreateCallbackDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::use('id', function () {
        });
        $this->assertInstanceOf(ReferenceDefinition::class, $definition);
    }

    public function testShouldFactoryCreateFnDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::fn(function () {
        });
        $this->assertInstanceOf(FnDefinition::class, $definition);
    }

    public function testShouldFactoryCreateClassDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::class(ClassA::class);
        $this->assertInstanceOf(ClassDefinition::class, $definition);
    }

    public function testShouldFactoryCreateFactoryDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::factory(FactoryClass::class, 'newObject');
        $this->assertInstanceOf(FactoryDefinition::class, $definition);
    }

    public function testShouldFactoryCreateIterateDefinition()
    {
        $definition = ClassUsingDefinitionBuilder::iterate('id1', 'id2');
        $this->assertInstanceOf(IterateDefinition::class, $definition);
    }

    public function testShouldGetErrorUndefinedDefinition()
    {
        $this->expectException(InvalidDefinitionException::class);
        ClassUsingDefinitionBuilder::undefined('id1', 'id2');
    }
}
