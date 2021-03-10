<?php
declare(strict_types=1);

namespace Habemus\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;

class TestCase extends PHPUnitTestCase
{
    protected static function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
