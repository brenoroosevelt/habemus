<?php
declare(strict_types=1);

namespace Habemus\Test;

use Exception;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

class TestCase extends PHPUnitTestCase
{
    protected function newContainerPsr11(array $values = []): ContainerInterface
    {
        return new class($values) implements ContainerInterface {
            protected $values = [];

            public function __construct(array $values)
            {
                $this->values = $values;
            }

            public function get($id)
            {
                if (array_key_exists($id, $this->values)) {
                    return $this->values[$id];
                }
                throw new class($id) extends Exception implements NotFoundExceptionInterface {
                    public function __construct($id)
                    {
                        parent::__construct(sprintf("Item (%s) not found", $id));
                    }
                };
            }

            public function has($id)
            {
                return array_key_exists($id, $this->values);
            }
        };
    }

    protected static function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    protected function getPropertyValue($object, $name)
    {
        $property = (new ReflectionClass($object))->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
