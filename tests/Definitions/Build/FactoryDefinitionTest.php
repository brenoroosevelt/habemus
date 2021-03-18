<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Build;

use Habemus\Container;
use Habemus\Definition\Build\FactoryDefinition;
use Habemus\Definition\Build\ReferenceDefinition;
use Habemus\Exception\ContainerException;
use Habemus\Exception\DefinitionException;
use Habemus\Exception\NotFoundException;
use Habemus\Test\Fixtures\FactoryClass;
use Habemus\Test\TestCase;
use InvalidArgumentException;
use stdClass;

class FactoryDefinitionTest extends TestCase
{
    public function testShouldCreateFactoryDefinitionWithDefaults()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $this->assertEquals('newObject', $factory->getMethodName());
        $this->assertEquals(FactoryClass::class, $factory->getObjectOrClass());
        $this->assertEmpty($factory->getMethodParams());
        $this->assertFalse($factory->isStaticCall());
        $this->assertFalse($factory->isShared());
    }

    public function testShouldCreateFactoryDefinitionWithMethodParameters()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject', ['param1', 'param2']);
        $this->assertEquals(['param1', 'param2'], $factory->getMethodParams());
    }

    public function testShouldCreateFactoryDefinitionWithStaticCall()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject', [], true);
        $this->assertTrue($factory->isStaticCall());
    }

    public function testShouldAddMethodParamsToFactoryDefinition()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $factory->methodParams(['param1']);
        $this->assertEquals(['param1'], $factory->getMethodParams());
    }

    public function testShouldSetStaticCallToFactoryDefinition()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $factory->staticCall(true);
        $this->assertTrue($factory->isStaticCall());
    }

    public function testShouldFactoryDefinitionCreateNewObjectOfFactoryClass()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $factoryInstance = $this->invokeMethod($factory, 'factoryInstance', [new Container()]);
        $this->assertInstanceOf(FactoryClass::class, $factoryInstance);
    }

    public function testShouldFactoryDefinitionReturnClassNameWhenCallStatic()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $factory->staticCall(true);
        $factoryInstance = $this->invokeMethod($factory, 'factoryInstance', [new Container()]);
        $this->assertEquals(FactoryClass::class, $factoryInstance);
    }

    public function testShouldFactoryDefinitionUseFactoryObject()
    {
        $myFactory = new FactoryClass();
        $factory = new FactoryDefinition($myFactory, 'newObject');
        $factoryInstance = $this->invokeMethod($factory, 'factoryInstance', [new Container()]);
        $this->assertSame($myFactory, $factoryInstance);
    }

    public function testShouldFactoryDefinitionGetsFactoryObjectFromContainer()
    {
        $container =  new Container();
        $container->add('factoryClass', $myFactory = new FactoryClass());
        $factory = new FactoryDefinition(new ReferenceDefinition('factoryClass'), 'newObject');
        $factoryInstance = $this->invokeMethod($factory, 'factoryInstance', [$container]);
        $this->assertSame($myFactory, $factoryInstance);
    }

    public function testShouldNotFactoryDefinitionRecreateFactoryInstance()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $factoryInstance1 = $this->invokeMethod($factory, 'factoryInstance', [new Container()]);
        $factoryInstance2 = $this->invokeMethod($factory, 'factoryInstance', [new Container()]);
        $this->assertSame($factoryInstance1, $factoryInstance2);
    }

    public function testShouldNotFactoryDefinitionResolveParametersWithContainer()
    {
        $factory =
            new FactoryDefinition(
                FactoryClass::class,
                'newObject',
                ['param1', new ReferenceDefinition('containerParam')]
            );

        $container =  new Container();
        $container->add('containerParam', 500);

        $params = $this->invokeMethod($factory, 'resolveParams', [$container]);
        $this->assertSame(['param1', 500], $params);
    }

    public function testShouldFactoryDefinitionGetErrorIfClassNotExists()
    {
        $factory = new FactoryDefinition('InvalidFactory', 'newObject');
        $this->expectException(NotFoundException::class);
        $factory->getConcrete(new Container());
    }

    public function testShouldFactoryDefinitionGetErrorIfMethodNotExists()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'invalidMethod');
//        $this->expectException(ContainerException::class);
        $this->expectExceptionObject(
            DefinitionException::invalidMethodCall($factory, new FactoryClass(), 'invalidMethod')
        );
        $factory->getConcrete(new Container());
    }

    public function testShouldFactoryDefinitionCreateInstances()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject');
        $object1 = $factory->getConcrete(new Container());
        $object2 = $factory->getConcrete(new Container());
        $this->assertInstanceOf(stdClass::class, $object1);
        $this->assertInstanceOf(stdClass::class, $object2);
        $this->assertNotSame($object1, $object2);
    }

    public function testShouldFactoryDefinitionCreateInstanceWithStaticCall()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'createObject');
        $factory->staticCall(true);
        $object = $factory->getConcrete(new Container());
        $this->assertInstanceOf(stdClass::class, $object);
    }

    public function testShouldFactoryDefinitionCreateInstanceWithParameter()
    {
        $factory = new FactoryDefinition(FactoryClass::class, 'newObject', [100]);
        $object = $factory->getConcrete(new Container());
        $this->assertInstanceOf(stdClass::class, $object);
        $this->assertEquals(100, $object->value);
    }
}
