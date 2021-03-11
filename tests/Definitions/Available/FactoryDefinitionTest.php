<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Available;

use Habemus\Container;
use Habemus\Definition\Available\FactoryDefinition;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Test\Fixtures\FactoryClass;
use Habemus\Test\TestCase;

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
        $factory = new FactoryDefinition(new IdDefinition('factoryClass'), 'newObject');
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
                ['param1', new IdDefinition('containerParam')]
            );

        $container =  new Container();
        $container->add('containerParam', 500);

        $params = $this->invokeMethod($factory, 'resolveParams', [$container]);
        $this->assertSame(['param1', 500], $params);
    }
}
