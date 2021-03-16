<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\MethodCall;

use Closure;
use Habemus\Container;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Definition\Definition;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Definition\MethodCall\CallableMethod;
use Habemus\Definition\MethodCall\CallableMethodTrait;
use Habemus\Exception\DefinitionException;
use Habemus\Test\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class CallableMethodTraitTest extends TestCase
{
    protected function newTraitInstance()
    {
        return new class {
            use CallableMethodTrait;
        };
    }

    protected function newObjectInstance()
    {
        return new class {
            public $value1 = 'default1';
            public $value2 = 'default2';

            public function withoutParams()
            {
            }

            public function singleParam($value1)
            {
                $this->value1 = $value1;
            }

            public function multiParams($value1, $value2)
            {
                $this->value1 = $value1;
                $this->value2 = $value2;
            }

            public function variadicParams(...$value1)
            {
                $this->value1 = $value1;
            }

            public function allTogether($value2, ...$value1)
            {
                $this->value1 = $value1;
                $this->value2 = $value2;
            }
        };
    }

    public function testShouldCallableMethodTraitImplementsCallableMethod()
    {
        $object = new class implements CallableMethod {
            use CallableMethodTrait;
        };
        $this->assertInstanceOf(CallableMethod::class, $object);
    }

    public function testShouldGetAValidEmptyMethodCall()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $callback = $trait->getMethodCall();
        $callback($object, new Container());
        $this->assertInstanceOf(Closure::class, $callback);
    }

    public function testShouldCallMethodWithSingleParameter()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $trait->addMethodCall('singleParam', [1]);
        ($trait->getMethodCall())($object, new Container());
        $this->assertSame(1, $object->value1);
    }

    public function testShouldCallMethodWithoutParameter()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $trait->addMethodCall('withoutParams');
        ($trait->getMethodCall())($object, new Container());
        $this->assertSame('default1', $object->value1);
    }

    public function testShouldCallMethodManyParameter()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $param =new stdClass();
        $trait->addMethodCall('multiParams', [1, $param]);
        ($trait->getMethodCall())($object, new Container());
        $this->assertSame(1, $object->value1);
        $this->assertSame($param, $object->value2);
    }

    public function testShouldCallMethodWithVariadicParameter()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $param1 =new stdClass();
        $param2 =new stdClass();
        $param3 =new stdClass();
        $trait->addMethodCall('variadicParams', [$param1, $param2, $param3]);
        ($trait->getMethodCall())($object, new Container());
        $this->assertSame([$param1, $param2, $param3], $object->value1);
    }

    public function testShouldCallMethodWithParameter()
    {
        $trait = $this->newTraitInstance();
        $object = $this->newObjectInstance();
        $param1 =new stdClass();
        $param2 =new stdClass();
        $param3 =new stdClass();
        $trait->addMethodCall('allTogether', [$param1, $param2, $param3]);
        ($trait->getMethodCall())($object, new Container());
        $this->assertSame($param1, $object->value2);
        $this->assertSame([$param2, $param3], $object->value1);
    }

    public function testShouldMethodCallGetParameterFromContainer()
    {
        $object = $this->newObjectInstance();
        $container = new Container();
        $container->add("id2", 100);

        $trait = $this->newTraitInstance();
        $trait->addMethodCall("multiParams", [new IdDefinition('id2'), 2]);

        $callback = $trait->getMethodCall();
        $callback($object, $container);
        $this->assertEquals(100, $object->value1);
        $this->assertEquals(2, $object->value2);
    }

    public function testShouldCallMethodThrowErrorIfMethodDoesNotExists()
    {
        $trait = new class implements Definition, CallableMethod{
            use IdentifiableTrait;
            use CallableMethodTrait;

            public function getConcrete(ContainerInterface $container)
            {
                return 1;
            }
        };
        $object = $this->newObjectInstance();
        $trait->addMethodCall('invalidMethod', [1]);
        $this->expectException(DefinitionException::class);
        ($trait->getMethodCall())($object, new Container());
    }
}
