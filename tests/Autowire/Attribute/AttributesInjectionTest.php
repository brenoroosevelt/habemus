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
use ReflectionClass;

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
     * @param $property
     * @param $expected
     */
    public function testShouldGetInjectionFromAttributesOrTypeHint($property, $expected)
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $inject = $this->attributesInjection->getInjection($property);
        $this->assertEquals($expected, $inject);
    }
}
