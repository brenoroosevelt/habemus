<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Container;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Exception\NotFound;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Util\PHPVersion;
use Psr\Container\ContainerInterface;
use ReflectionClass;

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

    public function testShouldContainerAddDefinition()
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
}
