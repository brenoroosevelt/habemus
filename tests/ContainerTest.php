<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Container;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Exception\NotFound;
use Habemus\ServiceProvider\LazyServiceProvider;
use Habemus\ServiceProvider\ServiceProvider;
use Habemus\Test\Fixtures\ClassA;
use Psr\Container\ContainerInterface;

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
        $instance = $container->get(ClassA::class);
        $resolvedList = $this->getPropertyValue($container, 'resolved');
        $this->assertTrue($container->defaultShared());
        $this->assertSame($resolvedList->get(ClassA::class), $instance);
    }

    public function testShouldContainerDisableDefaultShare()
    {
        $container = new Container();
        $container->useDefaultShared(false);
        $container->useAutowire(true);
        $container->get(ClassA::class);
        $resolvedList = $this->getPropertyValue($container, 'resolved');
        $this->assertFalse($container->defaultShared());
        $this->assertFalse($resolvedList->has(ClassA::class));
    }

    public function testShouldContainerDisableAutowire()
    {
        $container = new Container();
        $container->useAutowire(false);
        $this->assertFalse($container->autowireEnabled());
        $this->expectException(NotFound::class);
        $container->get(ClassA::class);
    }

    public function testShouldContainerEnableAutowire()
    {
        $container = new Container();
        $container->useAutowire(true);
        $instance = $container->get(ClassA::class);
        $this->assertTrue($container->autowireEnabled());
        $this->assertInstanceOf(ClassA::class, $instance);
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
}
