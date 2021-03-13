<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire\Attribute;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\Attributes\Inject;
use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassWithAttributes;
use Habemus\Test\TestCase;
use Habemus\Util\PHPVersion;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

class AttributesInjectionTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjection;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->reflector = new Reflector();
        $this->attributesInjection = new AttributesInjection($this->container, $this->reflector);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->attributesInjection);
        unset($this->reflector);
        unset($this->container);
        parent::tearDown();
    }

    public function getAttributesFromPropertiesProvider(): array
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            return [];
        }

        $class = new ReflectionClass(ClassWithAttributes::class);
        $properties = $class->getProperties();

        return [
            'injection_a' => [
                $properties[0],
                'id1',
            ],
            'injection_b' => [
                $properties[1],
                'id1',
            ],
            'injection_c' => [
                $properties[2],
                'id1',
            ],
            'injection_d' => [
                $properties[3],
                null,
            ],
            'injection_e' => [
                $properties[4],
                null,
            ],
            'injection_f' => [
                $properties[5],
                null,
            ],
            'injection_g' => [
                $properties[6],
                ClassA::class,
            ],
            'injection_h' => [
                $properties[7],
                ClassA::class,
            ],
            'injection_i' => [
                $properties[8],
                ClassA::class,
            ],
        ];
    }

    /**
     * @dataProvider getAttributesFromPropertiesProvider
     * @param ReflectionProperty $property
     * @param $expected
     */
    public function testShouldGetInjectionFromPropertiesAttributes(ReflectionProperty $property, $expected)
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $inject = $this->attributesInjection->getInjection($property);
        $this->assertEquals($expected, $inject);
    }

    public function getAttributesFromConstructorProvider(): array
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            return [];
        }

        $class = new ReflectionClass(ClassWithAttributes::class);
        $parametes = $class->getConstructor()->getParameters();

        return [
            'param_1' => [
                $parametes[0],
                'id1',
            ],
            'param_2' => [
                $parametes[1],
                ClassA::class,
            ],
            'param_3' => [
                $parametes[2],
                null,
            ]
        ];
    }

    /**
     * @dataProvider getAttributesFromConstructorProvider
     * @param ReflectionParameter $parameter
     * @param $expected
     */
    public function testShouldGetInjectionFromConstructorParameters(ReflectionParameter $parameter, $expected)
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $inject = $this->attributesInjection->getInjection($parameter);
        $this->assertEquals($expected, $inject);
    }

    public function testShouldInjectDependenciesOnObjectProperties()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        // arrange
        $classA = new ClassA();
        $object = new ClassWithAttributes(1, new ClassA(), 'str');
        $this->container->add('id1', 'value1');
        $this->container->add(ClassA::class, $classA);

        // action
        $this->attributesInjection->injectProperties($object);

        // assert
        $this->assertEquals('value1', $object->a());
        $this->assertEquals('value1', $object->b());
        $this->assertEquals('value1', $object->c());
        $this->assertNull($object->d());
        $this->assertNull($object->e());
        $this->assertNull($object->f());
        $this->assertSame($classA, $object->g());
        $this->assertSame($classA, $object->h());
        $this->assertSame($classA, $object->i());
    }

    public function testShouldGetErrorIfDependencyDoesNotExistWhenInjectingProperty()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        // arrange
        $object = new ClassWithAttributes(1, new ClassA(), 'str');

        // action
        $this->expectException(NotFoundExceptionInterface::class);
        $this->attributesInjection->injectProperties($object); // 'id1' does not exists
    }
}
