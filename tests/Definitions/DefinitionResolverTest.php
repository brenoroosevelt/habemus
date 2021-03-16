<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Autowiring\Reflector;
use Habemus\Container;
use Habemus\Definition\Available\FnDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionResolver;
use Habemus\ResolvedList;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\TestCase;
use Habemus\Utility\PHPVersion;
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
        $value = $this->definitionResolver->resolve((new RawDefinition("value"))->setIdentity('id1'));
        $this->assertSame("value", $value);
    }

    public function testShouldResolveManyDefinitions()
    {
        $definitions = [
            (new RawDefinition(1))->setIdentity('id1'),
            (new FnDefinition(function () {
                return new stdClass();
            }))->setIdentity('id2')
        ];

        $this->container->useAttributes(false);
        $resolved = $this->definitionResolver->resolveMany(...$definitions);
        $this->assertCount(2, $resolved);
        $this->assertEquals(1, $resolved[0]);
        $this->assertInstanceOf(stdClass::class, $resolved[1]);
    }

    public function testShouldResolveManyDefinitionsSharing()
    {
        $definitions = [
            (new RawDefinition(1))->setShared(true)->setIdentity('id1'),
            (new FnDefinition(function () {
                return new stdClass();
            }))->setShared(true)->setIdentity('id2')
        ];

        $this->container->useAttributes(false);
        $this->definitionResolver->resolveMany(...$definitions);
        $this->assertTrue($this->resolvedList->has('id1'));
        $this->assertTrue($this->resolvedList->has('id2'));
    }

    public function testShouldResolveAndShareDefinition()
    {
        $value = new stdClass();
        $definition = (new RawDefinition($value))->setShared(true)->setIdentity('id1');
        $instance = $this->definitionResolver->resolve($definition);
        $this->assertTrue($this->resolvedList->has('id1'));
        $this->assertSame($value, $this->resolvedList->get('id1'));
        $this->assertSame($value, $instance);
    }

    public function testShouldResolveAndAlwaysShareRawDefinition()
    {
        $definition = (new RawDefinition("value"))->setShared(false)->setIdentity('id1');
        $this->definitionResolver->resolve($definition);
        $this->assertTrue($this->resolvedList->has('id1'));
    }

    public function testShouldResolveAndNotShareDefinition()
    {
        $definition = (new FnDefinition(function () {
            return "value";
        }))->setShared(false)->setIdentity('id1');
        $this->definitionResolver->resolve($definition);
        $this->assertFalse($this->resolvedList->has('id1'));
    }

    public function testShouldResolveAndCallMethod()
    {
        $object = new ClassA();
        $definition = (new RawDefinition($object))->setIdentity('id1');
        $definition->addMethodCall('method'); //should call $object::method()
        $this->definitionResolver->resolve($definition);
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
        $definition->setIdentity('id1');
        // act
        $this->definitionResolver->resolve($definition);
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
        $definition = (new RawDefinition($object))->setIdentity('id1');
        // act
        $this->definitionResolver->resolve($definition);
        // assert
        $this->assertNull($object->property());
    }
}
