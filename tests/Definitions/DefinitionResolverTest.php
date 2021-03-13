<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionResolver;
use Habemus\ResolvedList;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use Habemus\Util\PHPVersion;
use stdClass;

class DefinitionResolverTest extends TestCase
{
    /**
     * @var DefinitionResolver
     */

    protected $definitionResolver;
    /**
     * @var Container
     */

    protected $container;

    /**
     * @var ResolvedList
     */
    protected $resolvedList;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjecton;

    /**
     * @var Reflector
     */
    protected $reflector;

    public function setUp(): void
    {
        $this->container = new Container();
        $this->resolvedList = new ResolvedList();
        $this->reflector = new Reflector();
        $this->attributesInjecton = new AttributesInjection($this->container, $this->reflector);
        $this->definitionResolver
            = new DefinitionResolver($this->container, $this->resolvedList, $this->attributesInjecton);
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->definitionResolver);
        unset($this->attributesInjecton);
        unset($this->reflector);
        unset($this->resolvedList);
        unset($this->container);
        parent::tearDown();
    }

    public function testShouldResolveADefinition()
    {
        $value = $this->definitionResolver->resolve('id1', new RawDefinition("value"));
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

        $this->container->useAttributes(false);
        $resolved = $this->definitionResolver->resolveMany($definitions);
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

        $this->container->useAttributes(false);
        $this->definitionResolver->resolveMany($definitions);
        $this->assertTrue($this->resolvedList->has('id1'));
        $this->assertTrue($this->resolvedList->has('id2'));
        $this->assertTrue($this->resolvedList->has('id3'));
    }

    public function testShouldResolveAndShareDefinition()
    {
        $definition = (new RawDefinition("value"))->setShared(true);
        $this->definitionResolver->resolve('id1', $definition);
        $this->assertTrue($this->resolvedList->has('id1'));
    }

    public function testShouldResolveAndAlwaysShareRawDefinition()
    {
        $definition = (new RawDefinition("value"))->setShared(false);
        $this->definitionResolver->resolve('id1', $definition);
        $this->assertTrue($this->resolvedList->has('id1'));
    }

    public function testShouldResolveAndNotShareDefinition()
    {
        $definition = (new FnDefinition(function () {
            return "value";
        }))->setShared(false);
        $this->definitionResolver->resolve('id1', $definition);
        $this->assertFalse($this->resolvedList->has('id1'));
    }

    public function testShouldResolveAndCallMethod()
    {
        $object = new ClassA();
        $definition = new RawDefinition($object);
        $definition->addMethodCall('method'); //should call $object::method()
        $this->definitionResolver->resolve('id1', $definition);
        $this->assertEquals(1, $object->value);
    }

    public function testShouldResolveAndInjectProperties()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        // arrange
        $this->container->useAttributes(true);
        $this->container->add('property_id', "property injection");
        $object = new ClassA();
        $definition = new FnDefinition(function () use ($object) {
            return $object;
        });
        // act
        $this->definitionResolver->resolve('id1', $definition);
        // assert
        $this->assertEquals("property injection", $object->property());
    }

    public function testShouldResolveNotInjectPropertiesRawDefinition()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        // arrange
        $this->container->useAttributes(true);
        $this->container->add('property_id', "property injection");
        $object = new ClassA();
        $definition = new RawDefinition($object);
        // act
        $this->definitionResolver->resolve('id1', $definition);
        // assert
        $this->assertNull($object->property());
    }
}
