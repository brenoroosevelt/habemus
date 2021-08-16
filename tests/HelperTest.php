<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Utility\PHPVersion;
use function Habemus\Utility\container;
use function Habemus\Utility\injectDependency;
use function Habemus\Utility\invoke;

class HelperTest extends TestCase
{
    public function testShouldGetSingleInstance()
    {
        $instance1 = container();
        $instance2 = container();

        $this->assertSame($instance1, $instance2);
    }

    public function testShouldUseInjection()
    {
        if (PHPVersion::current() >= PHPVersion::V8_0) {
            container()->add('property_id', 200);

            $object = new ClassA();
            injectDependency($object);

            $this->assertEquals(200, $object->property());
        }

        $this->markTestSkipped();
    }

    public function testShouldUseInvoke()
    {
        $result = invoke(function (
            ClassC $c
        ) {
            return 10;
        });

        $this->assertEquals(10, $result);
    }
}
