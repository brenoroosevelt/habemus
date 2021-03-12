<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire;

use Closure;
use Habemus\Autowire\Reflector;
use Habemus\Test\Fixtures\AbstractClass;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassB;
use Habemus\Test\Fixtures\GenericInterface;
use Habemus\Test\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionProperty;
use RuntimeException;

class ReflectorTest extends TestCase
{
    public function testShouldReflectorThrowsErrorDetermineAttributesAvailable()
    {
        $reflector = new Reflector();
        $this->assertEquals((PHP_VERSION_ID >= 80000), $reflector->attributesAvailable());
    }

    public function testShouldReflectorThrowsErrorIfAttributesNotAvailable()
    {
        $reflector =
            $this->getMockBuilder(Reflector::class)
                ->setMethods(['attributesAvailable'])
                ->getMock();

        $reflector
            ->expects($this->any())
            ->method('attributesAvailable')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $reflector->assertAttributesAvailable();
    }

    public function typeHintFromParameterProvider()
    {
        return [
            'primitive_string' => [
                function (string $x) {
                },
                true,
                'string',
            ],
            'primitive_float' => [
                function (float $x) {
                },
                true,
                'float',
            ],
            'primitive_array' => [
                function (array $x) {
                },
                true,
                'array',
            ],
            'primitive_int' => [
                function (int $x) {
                },
                true,
                'int',
            ],
            'primitive_int_optional' => [
                function (int $x = null) {
                },
                true,
                'int',
            ],
            'primitive_variadic_int' => [
                function (int ...$x) {
                },
                true,
                'int',
            ],
            'primitive_variadic_int_nullable' => [
                function (?int ...$x) {
                },
                true,
                'int'
            ],
            'non_primitive_string' => [
                function (string $x) {
                },
                false,
                null,
            ],
            'primitive_untyped' => [
                function ($x) {
                },
                true,
                null,
            ],
            'primitive_untyped_optional' => [
                function ($x = 10) {
                },
                true,
                null,
            ],
            'non_primitive_untyped' => [
                function ($x) {
                },
                false,
                null,
            ],
            'self_type' => [
                function (self $x) {
                },
                true,
                self::class,
            ],
            'self_type_nullable' => [
                function (?self $x) {
                },
                true,
                self::class,
            ],
            'self_type_nullable_variadic' => [
                function (?self ...$x) {
                },
                true,
                self::class,
            ],
            'class_name' => [
                function (ClassA $classA) {
                },
                true,
                ClassA::class,
            ],
            'class_name_nullable' => [
                function (?ClassA $classA) {
                },
                true,
                ClassA::class,
            ],
            'class_name_variadic' => [
                function (ClassA ...$classA) {
                },
                true,
                ClassA::class,
            ],
            'class_name_variadic_nullable' => [
                function (?ClassA ...$classA) {
                },
                true,
                ClassA::class,
            ],
            'interface' => [
                function (GenericInterface $i) {
                },
                true,
                GenericInterface::class,
            ],
            'abstract_class' => [
                function (AbstractClass $a) {
                },
                true,
                AbstractClass::class,
            ],
        ];
    }

    /**
     * @dataProvider typeHintFromParameterProvider
     * @param Closure $fn
     * @param bool $primitive
     * @param $expected
     * @throws ReflectionException
     */
    public function testShouldReflectorDetermineTypeHintFromParameter(Closure $fn, bool $primitive, $expected)
    {
        $reflector = new Reflector();
        $parameter = (new ReflectionFunction($fn))->getParameters()[0];
        $typeHint = $reflector->getTypeHint($parameter, $primitive);
        $this->assertEquals($expected, $typeHint);
    }

    public function typeHintFromPropertiesProvider(): array
    {
        if (PHP_VERSION_ID < 70400) {
            return [];
        }

        $class = new ReflectionClass(\Habemus\Test\Fixtures\ClassTypedProperties::class);
        $properties = $class->getProperties();

        return [
            'undefined' => [
                $properties[0],
                true,
                null
            ],
            'int' => [
                $properties[1],
                true,
                'int'
            ],
            'float' => [
                $properties[2],
                true,
                'float'
            ],
            'ClassA' => [
                $properties[3],
                true,
                ClassA::class
            ],
            'array' => [
                $properties[4],
                true,
                'array'
            ],
            'ClassB' => [
                $properties[5],
                true,
                ClassB::class
            ],
            'GenericInterface' => [
                $properties[6],
                true,
                GenericInterface::class
            ],
            'AbstractClass' => [
                $properties[7],
                true,
                AbstractClass::class
            ],
            'self' => [
                $properties[8],
                true,
                \Habemus\Test\Fixtures\ClassTypedProperties::class
            ],
            'self_nullable' => [
                $properties[9],
                true,
                \Habemus\Test\Fixtures\ClassTypedProperties::class
            ],

        ];
    }

    /**
     * @dataProvider typeHintFromPropertiesProvider
     * @param ReflectionProperty $property
     * @param bool $primitive
     * @param $expected
     */
    public function testShouldReflectorDetermineTypeHintFromProperties(
        ReflectionProperty $property,
        bool $primitive,
        $expected
    ) {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped('Typed properties are not available (PHP 7.4+)');
            return;
        }

        $reflector = new Reflector();
        $typeHint = $reflector->getTypeHint($property, $primitive);
        $this->assertEquals($expected, $typeHint);
    }

    public function testShouldReflectorDetermineTypeHintFromPropertiesSubClass()
    {
        if (PHP_VERSION_ID < 70400) {
            $this->markTestSkipped('Typed properties are not available (PHP 7.4+)');
            return;
        }

        $class = new ReflectionClass(\Habemus\Test\Fixtures\SubClassTypedProperties::class);
        $properties = $class->getProperties();

        $reflector = new Reflector();
        $this->assertEquals(
            \Habemus\Test\Fixtures\SubClassTypedProperties::class,
            $reflector->getTypeHint($properties[0], true)
        );
        $this->assertEquals(
            \Habemus\Test\Fixtures\ClassTypedProperties::class,
            $reflector->getTypeHint($properties[9], true)
        );
        $this->assertEquals(
            \Habemus\Test\Fixtures\ClassTypedProperties::class,
            $reflector->getTypeHint($properties[10], true)
        );
    }
}
