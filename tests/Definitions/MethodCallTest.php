<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Container;
use Habemus\Definition\Available\IdDefinition;
use Habemus\Definition\Available\RawDefinition;
use PHPUnit\Framework\TestCase;

class MethodCallTest extends TestCase
{
    public function testShouldMethodCallGetParameterFromContainer()
    {
        $object = new class {
            private $values = [];

            public function addValue(int ...$v)
            {
                foreach ($v as $_v) {
                    array_push($this->values, $_v);
                }
            }

            public function getValues(): array
            {
                return $this->values;
            }
        };

        $container = new Container();
        $container->add("id2", 1);

        $definition = new RawDefinition($object);
        $definition->addMethodCall("addValue", [Container::id('id2'), 2, Container::id('id2')]);

        $callback = $definition->getMethodCall();
        $callback($object, $container);
        $this->assertEquals([1, 2, 1], $object->getValues());
    }

    public function testShouldMethodCallReturnsDefaultEmptyCallback()
    {
        $definition = new IdDefinition("anId");
        $callback = $definition->getMethodCall();
        $callback(new \stdClass(), new Container);
        $this->assertInstanceOf(\Closure::class, $callback);
    }
}
