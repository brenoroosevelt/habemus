<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionResolver;
use Habemus\ResolvedList;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;

class DefinitionResolverTest extends TestCase
{
    public function testShouldCreateAnInstanceOfResolver()
    {
        $resolver = new DefinitionResolver(new Container(), new ResolvedList());
        $this->assertInstanceOf(DefinitionResolver::class, $resolver);
    }

    public function testShouldResolveADefinition()
    {
        $resolver = new DefinitionResolver(new Container(), new ResolvedList());
        $value = $resolver->resolve('id1', new RawDefinition("value"));
        $this->assertSame("value", $value);
    }

    public function testShouldResolveAndShareDefinition()
    {
        $resolvedList = new ResolvedList();
        $resolver = new DefinitionResolver(new Container(), $resolvedList);
        $definition = (new RawDefinition("value"))->setShared(true);
        $resolver->resolve('id1', $definition);
        $this->assertTrue($resolvedList->has('id1'));
    }

    public function testShouldResolveAndAlwaysShareRawDefinition()
    {
        $resolvedList = new ResolvedList();
        $resolver = new DefinitionResolver(new Container(), $resolvedList);
        $definition = (new RawDefinition("value"))->setShared(false);
        $resolver->resolve('id1', $definition);
        $this->assertTrue($resolvedList->has('id1'));
    }

    public function testShouldResolveAndNotShareDefinition()
    {
        $resolvedList = new ResolvedList();
        $resolver = new DefinitionResolver(new Container(), $resolvedList);
        $definition = (new FnDefinition(function () {
            return "value";
        }))->setShared(false);
        $resolver->resolve('id1', $definition);
        $this->assertFalse($resolvedList->has('id1'));
    }

    public function testShouldResolveAndCallMethod()
    {
        $resolvedList = new ResolvedList();
        $resolver = new DefinitionResolver(new Container(), $resolvedList);
        $object = new ClassA();
        $definition = new RawDefinition($object);
        $definition->addMethodCall('method'); //should call $object::method()
        $resolver->resolve('id1', $definition);
        $this->assertEquals(1, $object->value);
    }

    public function testShouldResolveAndInjectProperties()
    {
        if (!Reflector::attributesAvailable()) {
            $this->assertTrue(true); //skip test... property injection: php >=8.0
            return;
        }

        // arrange
        $container = new Container();
        $container->useAttributes(true);
        $container->add('property_id', "property injection");
        $resolvedList = new ResolvedList();
        $object = new ClassA();
        $definition = new FnDefinition(function () use ($object) {
            return $object;
        });
        // act
        $resolver = new DefinitionResolver($container, $resolvedList);
        $resolver->resolve('id1', $definition);
        // assert
        $this->assertEquals("property injection", $object->property());
    }

    public function testShouldResolveNotInjectPropertiesRawDefinition()
    {
        if (!Reflector::attributesAvailable()) {
            $this->assertTrue(true); //skip test... property injection: php >=8.0
            return;
        }

        // arrange
        $container = new Container();
        $container->useAttributes(true);
        $container->add('property_id', "property injection");
        $resolvedList = new ResolvedList();
        $object = new ClassA();
        $definition = new RawDefinition($object);
        // act
        $resolver = new DefinitionResolver($container, $resolvedList);
        $resolver->resolve('id1', $definition);
        // assert
        $this->assertNull($object->property());
    }
}
