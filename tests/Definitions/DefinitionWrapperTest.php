<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Container;
use Habemus\Definition\Available\ArrayDefinition;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\Definition;
use Habemus\Definition\DefinitionWrapper;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Exception\DefinitionException;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use Psr\Container\ContainerInterface;

class DefinitionWrapperTest extends TestCase
{
    public function testShouldDefinitionWrapperAddConstructorParameters()
    {
        $definition = new ClassDefinition(ClassA::class);
        $wrapper = new DefinitionWrapper($definition);
        $wrapper->constructor('param1', 'value1');
        $wrapper->constructor('param2', 'value2');
        $this->assertEquals([
            'param1' => 'value1',
            'param2' => 'value2',
        ], $definition->getConstructorParameters());
    }

    public function testShouldDefinitionWrapperSetShared()
    {
        $definition = new RawDefinition('value');
        $wrapper = new DefinitionWrapper($definition);
        $wrapper->shared(true);
        $this->assertTrue($definition->isShared());
    }

    public function testShouldDefinitionWrapperSetNotShared()
    {
        $definition = new RawDefinition('value');
        $wrapper = new DefinitionWrapper($definition);
        $wrapper->shared(false);
        $this->assertFalse($definition->isShared());
    }

    public function testShouldDefinitionWrapperAddMethodCall()
    {
        $object = new ClassA();
        $definition = new ClassDefinition(ClassA::class);
        $wrapper = new DefinitionWrapper($definition);
        $wrapper->addMethodCall('method');
        $callback = $definition->getMethodCall();
        $callback($object, new Container());
        $this->assertEquals(1, $object->value);
    }

    public function testShouldDefinitionWrapperAddTags()
    {
        $definition = new RawDefinition("value");
        $wrapper = new DefinitionWrapper($definition);
        $wrapper->addTag('tag1', 'tag2')->addTag('tag3');
        $this->assertTrue($definition->hasTag('tag1'));
        $this->assertTrue($definition->hasTag('tag2'));
        $this->assertTrue($definition->hasTag('tag3'));
        $this->assertCount(3, $definition->getTags());
    }

    public function testShouldDefinitionWrapperThrowExceptionWhenMethodCallNotAvailable()
    {
        $definition = new ArrayDefinition(["value"]);
        $wrapper = new DefinitionWrapper($definition);
        $this->expectExceptionObject(DefinitionException::unavailableMethodCall($definition));
        $wrapper->addMethodCall('method', ['param']);
    }

    public function testShouldDefinitionWrapperThrowExceptionWhenUnsharable()
    {
        $definition = new class implements Definition {
            use IdentifiableTrait;
            public function getConcrete(ContainerInterface $container)
            {
                return 1;
            }
        };

        $wrapper = new DefinitionWrapper($definition);
        $this->expectExceptionObject(DefinitionException::unshareable($definition));
        $wrapper->shared(true);
    }

    public function testShouldDefinitionWrapperThrowExceptionWhenConstructorParametersIsNotAvailable()
    {
        $definition = new class implements Definition {
            use IdentifiableTrait;
            public function getConcrete(ContainerInterface $container)
            {
                return 1;
            }
        };

        $wrapper = new DefinitionWrapper($definition);
        $this->expectExceptionObject(DefinitionException::unavailableConstructorParameters($definition));
        $wrapper->constructor('param1', 'value1');
    }

    public function testShouldDefinitionWrapperThrowExceptionWhenUntaggable()
    {
        $definition = new class implements Definition {
            use IdentifiableTrait;
            public function getConcrete(ContainerInterface $container)
            {
                return 1;
            }
        };

        $wrapper = new DefinitionWrapper($definition);
        $this->expectExceptionObject(DefinitionException::untaggable($definition));
        $wrapper->addTag('tag1', 'tag2');
    }
}
