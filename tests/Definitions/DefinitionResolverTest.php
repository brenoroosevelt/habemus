<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Container;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionResolver;
use Habemus\ResolvedList;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use stdClass;

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

    public function testShouldResolveManyDefinitions()
    {
        $definitions = [
            'id1' => new RawDefinition(1),
            'id2' => new FnDefinition(function () {
                return new stdClass();
            }),
            'id3' => new ClassDefinition(ClassA::class)
        ];

        $resolver = new DefinitionResolver(new Container(), new ResolvedList());
        $resolved = $resolver->resolveMany($definitions);
        $this->assertCount(3, $resolved);
        $this->assertEquals(1, $resolved[0]);
        $this->assertInstanceOf(stdClass::class, $resolved[1]);
        $this->assertInstanceOf(ClassA::class, $resolved[2]);
    }

    public function testShouldResolveManyDefinitionsSharing()
    {
        $definitions = [
            'id1' => (new RawDefinition(1))->setShared(true),
            'id2' => (new FnDefinition(function () {
                return new stdClass();
            }))->setShared(true),
            'id3' => (new ClassDefinition(ClassA::class))->setShared(true)
        ];

        $resolvedList = new ResolvedList();
        $resolver = new DefinitionResolver(new Container(), $resolvedList);
        $resolver->resolveMany($definitions);
        $this->assertTrue($resolvedList->has('id1'));
        $this->assertTrue($resolvedList->has('id2'));
        $this->assertTrue($resolvedList->has('id3'));
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
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes available in PHP version >= 8.0');
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
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes available in PHP version >= 8.0');
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
