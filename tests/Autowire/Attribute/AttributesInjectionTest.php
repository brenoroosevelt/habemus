<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire\Attribute;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\Attributes\Inject;
use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Exception\InjectionException;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Test\Fixtures\ClassUseTrait;
use Habemus\Test\Fixtures\ClassWithAttributes;
use Habemus\Test\TestCase;
use Habemus\Util\PHPVersion;
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
                'id2',
            ],
            'injection_e' => [
                $properties[4],
                'id2',
            ],
            'injection_f' => [
                $properties[5],
                'id2',
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
                'id2',
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
        $this->container->add('id2', 'value2');
        $this->container->add(ClassA::class, $classA);

        // action
        $this->attributesInjection->inject($object);

        // assert
        $this->assertEquals('value1', $object->a());
        $this->assertEquals('value1', $object->b());
        $this->assertEquals('value1', $object->c());
        $this->assertEquals('value2', $object->d());
        $this->assertEquals('value2', $object->e());
        $this->assertEquals('value2', $object->f());
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
        $this->expectException(InjectionException::class);
        $this->attributesInjection->inject($object); // 'id1', 'id2' does not exists
    }

    public function testShouldGetErrorIfTryInjectOnNonObject()
    {
        $this->expectException(InjectionException::class);
        $this->expectExceptionMessage("Expected object to inject property dependencies. Got (integer).");
        $this->attributesInjection->inject(123);
    }

    public function testShouldInjectOnTraitProperties()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $object = new ClassUseTrait();
        $this->attributesInjection->inject($object);
        $this->assertInstanceOf(ClassC::class, $object->a());
        $this->assertInstanceOf(ClassC::class, $object->b());
    }

    public function testShouldGetErrorWhenInjectionAttributeIsUndetermined()
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $object = new class {
            #[Inject]
            protected $a;
        };

        $this->expectException(InjectionException::class);
        $this->attributesInjection->inject($object);
    }
}
