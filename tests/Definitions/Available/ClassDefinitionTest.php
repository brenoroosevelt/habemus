<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Exception\NotFound;
use Habemus\Exception\UnresolvableParameter;
use Habemus\Test\Fixtures\ClassB;
use Habemus\Test\TestCase;

class ClassDefinitionTest extends TestCase
{
    public function testShouldCreateClassDefinitionDefaultConstructor()
    {
        $definition = new ClassDefinition(ClassB::class);
        $this->assertEmpty($definition->getConstructorParameters());
        $this->assertEquals(ClassB::class, $definition->class());
    }

    public function testShouldCreateClassDefinitionWithConstructorParameters()
    {
        $definition = new ClassDefinition(ClassB::class, ['param' => 'value']);
        $this->assertSame(['param' => 'value'], $definition->getConstructorParameters());
    }

    public function testShouldClassDefinitionAddConstructorParameters()
    {
        $definition = new ClassDefinition(ClassB::class);
        $definition->constructor('param', 'value')->constructor('param2', 'value2');
        $this->assertSame(['param' => 'value', 'param2' => 'value2'], $definition->getConstructorParameters());
    }

    public function testShouldClassDefinitionResolveInstance()
    {
        $container = new Container();
        $container->useAutowire(true);
        $definition = new ClassDefinition(ClassB::class, ['param' => 'value']);
        $instance = $definition->getConcrete($container);

        $this->assertInstanceOf(ClassB::class, $instance);
        $this->assertEquals('value', $instance->value);
    }

    public function testShouldNotClassDefinitionResolveInstanceWithoutParameters()
    {
        $container = new Container();
        $container->useAutowire(true);
        $definition = new ClassDefinition(ClassB::class);
        $this->expectException(UnresolvableParameter::class);
        $definition->getConcrete($container);
    }

    public function testShouldNotClassDefinitionResolveAnUnknownClass()
    {
        $container = new Container();
        $container->useAutowire(true);
        $definition = new ClassDefinition('UnknownClass');
        $this->expectException(NotFound::class);
        $definition->getConcrete($container);
    }
}
