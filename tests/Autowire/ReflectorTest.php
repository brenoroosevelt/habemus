<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire;

use Closure;
use Habemus\Autowire\Reflector;
use Habemus\Test\Fixtures\AbstractClass;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\GenericInterface;
use Habemus\Test\TestCase;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;

class ReflectorTest extends TestCase
{
    public function testShouldReflectorThrowsErrorDetermineAttributesAvailable()
    {
        $reflector = new Reflector();
        $this->assertEquals((PHP_VERSION_ID >= 800000), $reflector->attributesAvailable());
    }

    public function testShouldReflectorThrowsErrorIfAttributesNotAvailable()
    {
        $reflector =
            $this->getMockBuilder(Reflector::class)
                ->setMethods(['attributesAvailable'])
                ->disableOriginalConstructor()
                ->getMock();

        $reflector
            ->expects($this->any())
            ->method('attributesAvailable')
            ->will($this->returnValue(false));

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
}
