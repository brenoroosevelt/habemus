<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Container;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Exception\NotFound;
use Habemus\ServiceProvider\LazyServiceProvider;
use Habemus\ServiceProvider\ServiceProvider;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Test\Fixtures\ClassWithAttributes;
use Habemus\Test\Fixtures\ConstructorSelfDependency;
use Habemus\Test\Fixtures\DependencyClassA;
use Habemus\Test\Fixtures\PropertySelfCircularDependency;
use Habemus\Util\PHPVersion;
use Psr\Container\ContainerInterface;
use RuntimeException;

class ContainerTest extends TestCase
{
    public function testShouldContainerInstanceOfPsr11()
    {
        $container = new Container();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testShouldContainerEnableDefaultShare()
    {
        $container = new Container();
        $container->useDefaultShared(true);
        $container->useAutowire(true);
        $instance = $container->get(ClassC::class);
        $resolvedList = $this->getPropertyValue($container, 'resolved');
        $this->assertTrue($container->defaultShared());
        $this->assertTrue($resolvedList->has(ClassC::class));
        $this->assertSame($resolvedList->get(ClassC::class), $instance);
    }

    public function testShouldContainerDisableDefaultShare()
    {
        $container = new Container();
        $container->useDefaultShared(false);
        $container->useAutowire(true);
        $container->get(ClassC::class);
        $resolvedList = $this->getPropertyValue($container, 'resolved');
        $this->assertFalse($container->defaultShared());
        $this->assertFalse($resolvedList->has(ClassC::class));
    }

    public function testShouldContainerDisableAutowire()
    {
        $container = new Container();
        $container->useAutowire(false);
        $this->assertFalse($container->autowireEnabled());
        $this->expectException(NotFound::class);
        $container->get(ClassC::class);
    }

    public function testShouldContainerEnableAutowire()
    {
        $container = new Container();
        $container->useAutowire(true);
        $instance = $container->get(ClassC::class);
        $this->assertTrue($container->autowireEnabled());
        $this->assertInstanceOf(ClassC::class, $instance);
    }

    public function testShouldContainerAddDefinitionValue()
    {
        $container = new Container();
        $container->add('id1', 'value1');
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerAddGetDefinition()
    {
        $container = new Container();
        $container->add('id1', new RawDefinition('value1'));
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerAddGetClassDefinition()
    {
        $container = new Container();
        $container->add('id1', ClassC::class);
        $this->assertTrue($container->has('id1'));
        $this->assertInstanceOf(ClassC::class, $container->get('id1'));
    }

    public function testShouldContainerAddRawDefinitionAsResolved()
    {
        $container = new Container();
        $container->add('id1', new RawDefinition('value1'));
        $resolvedList = $this->getPropertyValue($container, 'resolved');
        $this->assertTrue($resolvedList->has('id1'));
    }

    public function testShouldContainerAddServiceProvider()
    {
        $provider = new class implements ServiceProvider {
            public function register(Container $container): void
            {
                $container->add('id1', 'value1');
            }

        };
        $container = new Container();
        $container->addProvider($provider);
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerAddLazyServiceProvider()
    {
        $provider = new class implements LazyServiceProvider {
            public function register(Container $container): void
            {
                $container->add('id1', 'value1');
            }

            public function provides(string $id): bool
            {
                return $id === 'id1';
            }
        };
        $container = new Container();
        $container->addProvider($provider);
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerAddDelegateContainer()
    {
        $delegate = $this->newContainerPsr11(['id1' => 'value1']);
        $container = new Container();
        $container->addDelegate($delegate);
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerAddDelegateContainerWithPriority()
    {
        $delegate1 = $this->newContainerPsr11(['id1' => 'value1']);
        $delegate2 = $this->newContainerPsr11(['id1' => 'value2']);
        $delegate3 = $this->newContainerPsr11(['id1' => 'value3']);
        $container = new Container();
        $container->addDelegate($delegate1, 2);
        $container->addDelegate($delegate2, 1);
        $container->addDelegate($delegate3, 3);
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value2', $container->get('id1'));
    }

    public function testShouldContainerTakePriorityOverDelegates()
    {
        $delegate1 = $this->newContainerPsr11(['id1' => 'value1']);
        $delegate2 = $this->newContainerPsr11(['id1' => 'value2']);
        $delegate3 = $this->newContainerPsr11(['id1' => 'value3']);
        $container = new Container();
        $container->addDelegate($delegate1, 2);
        $container->addDelegate($delegate2, 1);
        $container->addDelegate($delegate3, 3);
        $container->add('id1', function () {
            return 'value4';
        });
        $this->assertTrue($container->has('id1'));
        $this->assertEquals('value4', $container->get('id1'));
    }

    public function testShouldContainerResolveTaggedDefinitions()
    {
        $container = new Container();
        $container->add('id1', 1)->addTag('tag1', 'tag2');
        $container->add('id2', 2)->addTag('tag2');
        $container->add('id3', 3)->addTag('tag1', 'tag3');

        $this->assertTrue($container->has('id1'));
        $this->assertTrue($container->has('id2'));
        $this->assertTrue($container->has('id3'));

        $this->assertTrue($container->has('tag1'));
        $this->assertTrue($container->has('tag2'));
        $this->assertTrue($container->has('tag3'));

        $this->assertEquals([1, 3], $container->get('tag1'));
        $this->assertEquals([1, 2], $container->get('tag2'));
        $this->assertEquals([3], $container->get('tag3'));
    }

    public function testShouldContainerAddGetArrayAccess()
    {
        $container = new Container();
        $container['id1'] = 'value1';
        $this->assertEquals('value1', $container['id1']);
        $this->assertEquals('value1', $container->get('id1'));
    }

    public function testShouldContainerCheckArrayAccess()
    {
        $container = new Container();
        $container['id1'] = 'value1';
        $this->assertTrue(isset($container['id1']));
        $this->assertTrue($container->has('id1'));
    }

    public function testShouldContainerDeleteArrayAccess()
    {
        $container = new Container();
        $container['id1'] = 'value1';
        unset($container['id1']);
        $this->assertFalse(isset($container['id1']));
        $this->assertFalse($container->has('id1'));
    }

    public function testShouldContainerDetectConstructorSelfCircularDependency()
    {
        $container = new Container();
        $this->expectException(RuntimeException::class);
        $container->get(ConstructorSelfDependency::class);
    }

    public function testShouldContainerDetectPropertySelfCircularDependency()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $container = new Container();
        $container->useAttributes(true);
        $container->useDefaultShared(false); // important! shared instance can avoid circular dependency
        $this->expectException(RuntimeException::class);
        var_dump($container->get(PropertySelfCircularDependency::class));
    }

    public function testShouldContainerDetectCircularDependency()
    {
        $container = new Container();
        $this->expectException(RuntimeException::class);
        $container->get(DependencyClassA::class);
    }

    public function testShouldInjectDependenciesOnObjectProperties()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        // arrange
        $container = new Container();
        $classA = new ClassA();
        $object = new ClassWithAttributes(1, new ClassA(), 'str');
        $container->add('id1', 'value1');
        $container->add(ClassA::class, $classA);

        // action
        $container->injectDependency($object);

        // assert
        $this->assertEquals('value1', $object->a());
        $this->assertEquals('value1', $object->b());
        $this->assertEquals('value1', $object->c());
        $this->assertNull($object->d());
        $this->assertNull($object->e());
        $this->assertNull($object->f());
        $this->assertSame($classA, $object->g());
        $this->assertSame($classA, $object->h());
        $this->assertSame($classA, $object->i());
    }
}
