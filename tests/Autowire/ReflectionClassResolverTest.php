<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire;

use Closure;
use Exception;
use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Autowiring\Attributes\Inject;
use Habemus\Autowiring\Parameter\ParameterResolverChain;
use Habemus\Autowiring\ReflectionClassResolver;
use Habemus\Autowiring\Reflector;
use Habemus\Container;
use Habemus\Exception\NotFoundException;
use Habemus\Exception\NotInstantiableException;
use Habemus\Exception\UnresolvableParameterException;
use Habemus\Test\Fixtures\AbstractClass;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Test\Fixtures\ClassWithoutConstructor;
use Habemus\Test\Fixtures\GenericInterface;
use Habemus\Test\Fixtures\PrivateConstructor;
use Habemus\Test\TestCase;
use Habemus\Utility\PHPVersion;
use ReflectionException;
use ReflectionFunction;

class ReflectionClassResolverTest extends TestCase
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

    /**
     * @var ReflectionClassResolver
     */
    protected $classResolver;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->reflector = new Reflector();
        $this->attributesInjection = new AttributesInjection($this->container, $this->reflector);
        $this->classResolver =
            new ReflectionClassResolver(
                ParameterResolverChain::default($this->container, $this->attributesInjection, $this->reflector)
            );
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->container);
        unset($this->reflector);
        unset($this->attributesInjection);
        unset($this->classResolver);
        parent::tearDown();
    }

    public function testShouldGetErrorIfTryInstantiateUnknownClass()
    {
        $this->expectException(NotFoundException::class);
        $this->classResolver->resolveClass('UnknownClass');
    }

    public function testShouldGetErrorIfTryInstantiateInterface()
    {
        $this->expectException(NotFoundException::class);
        $this->classResolver->resolveClass(GenericInterface::class);
    }

    public function testShouldGetErrorIfTryInstantiateAbstractClass()
    {
        $this->expectException(NotInstantiableException::class);
        $this->classResolver->resolveClass(AbstractClass::class);
    }

    public function testShouldGetErrorIfTryInstantiatePrivateConstructor()
    {
        $this->expectException(NotInstantiableException::class);
        $this->classResolver->resolveClass(PrivateConstructor::class);
    }

    public function testShouldInstantiateAClassWithoutConstructor()
    {
        $instance = $this->classResolver->resolveClass(ClassWithoutConstructor::class);
        $this->assertInstanceOf(ClassWithoutConstructor::class, $instance);
    }

    public function testShouldInstantiateAClassWithEmptyConstructor()
    {
        $instance = $this->classResolver->resolveClass(ClassA::class);
        $this->assertInstanceOf(ClassA::class, $instance);
    }

    public function resolveParameterProvider(): array
    {
        return [
            'empty_function_parameters' => [
                function () {
                },
                [],
                []
            ],
            'empty_function_parameters_2' => [
                function () {
                },
                ['a' => 1],
                []
            ],
            'unresolvable_primitive_type' => [
                function (int $a) {
                },
                [],
                UnresolvableParameterException::class
            ],
            'resolvable_primitive_with_arguments' => [
                function (int $a) {
                },
                ['a' => 1],
                [1]
            ],
            'variadic_parameter_single_value' => [
                function (int ...$a) {
                },
                ['a' => 1],
                [1]
            ],
            'variadic_parameter_array_value' => [
                function (int ...$a) {
                },
                ['a' => [1, 2, 3]],
                [1, 2, 3]
            ],
            'variadic_parameter_arrays' => [
                function (array ...$a) {
                },
                ['a' => [[1, 2, 3], [4, 5]] ],
                [[1, 2, 3], [4, 5]]
            ],
            'merge_variadic_parameters' => [
                function (string $a, int ...$b) {
                },
                ['a' => "str", 'b' => [1, 2, 3]],
                ["str", 1, 2, 3]
            ],
            'variadic_optional' => [
                function (string $a, int ...$b) {
                },
                ['a' => "str"],
                ["str"]
            ],
            'variadic_nullable_does_not_use_null' => [
                function (string $a, ?int ...$b) {
                },
                ['a' => "str"],
                ["str"]
            ],
            'nullable_non_optional_use_null' => [
                function (?string $a) {
                },
                [],
                [null]
            ],
            'nullable_use_default' => [
                function (?string $a = "str") {
                },
                [],
                ["str"]
            ],
            'use_default' => [
                function (string $a = "str") {
                },
                [],
                ["str"]
            ],
            'use_argument_instead_of_default' => [
                function (string $a = "str") {
                },
                ['a' => "strings"],
                ["strings"]
            ],
            'container_resolve_type' => [
                function (ClassC $a) {
                },
                [],
                [new ClassC()]
            ],
            'container_does_not_resolve_optional' => [
                function (ClassA $a = null) {
                },
                [],
                [null]
            ],
            'container_does_not_resolve_required_nullable' => [
                function (?ClassA $a) {
                },
                [],
                [null]
            ],
            'container_does_not_resolve_optional_nullable' => [
                function (?ClassA $a = null) {
                },
                [],
                [null]
            ],
            'container_does_not_resolve_unknown_class' => [
                function (GenericInterface $a) {
                },
                [],
                UnresolvableParameterException::class
            ],
        ];
    }

    /**
     * @dataProvider resolveParameterProvider
     * @param Closure $fn
     * @param array $arguments
     * @param $expected
     * @throws ReflectionException
     */
    public function testShouldResolveParameters(Closure $fn, array $arguments, $expected)
    {
        $reflectionFunction = new ReflectionFunction($fn);
        if (is_string($expected)) {
            $this->expectException($expected);
            $this->classResolver->resolveArguments($reflectionFunction, $arguments);
        } else {
            $actual = $this->classResolver->resolveArguments($reflectionFunction, $arguments);
            $this->assertEquals($expected, $actual);
        }
    }

    public function resolveParameterWithInjectionProvider(): array
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            return [];
        }
        // @codingStandardsIgnoreStart
        return [
            'attributes_injection_by_type_hint' => [
                function (
                    #[Inject]
                    ClassC $a
                ) {
                },
                [],
                [new ClassC()]
            ],
            'attributes_injection_by_id' => [
                function (
                    #[Inject(ClassC::class)]
                    $a
                ) {
                },
                [],
                [new ClassC()]
            ],
            'use_arguments_insted_of_injection' => [
                function (
                    #[Inject(ClassC::class)]
                    $a
                ) {
                },
                ['a' => 1 ],
                [1]
            ],
            'unresolvable_parameter_injection' => [
                function (
                    #[Inject(GenericInterface::class)]
                    $a
                ) {
                },
                [],
                UnresolvableParameterException::class
            ],
            'not_instantiable_parameter_injection' => [
                function (
                    #[Inject(AbstractClass::class)]
                    $a
                ) {
                },
                [],
                Exception::class
            ],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider resolveParameterWithInjectionProvider
     * @param Closure $fn
     * @param array $arguments
     * @param $expected
     * @throws ReflectionException
     */
    public function testShouldResolveParametersWithInjection(Closure $fn, array $arguments, $expected)
    {
        if (PHPVersion::current() < PHPVersion::V8_0) {
            $this->markTestSkipped('Attributes are not available (PHP 8.0+)');
            return;
        }

        $reflectionFunction = new ReflectionFunction($fn);
        if (is_string($expected)) {
            $this->expectException($expected);
            $this->classResolver->resolveArguments($reflectionFunction, $arguments);
        } else {
            $actual = $this->classResolver->resolveArguments($reflectionFunction, $arguments);
            $this->assertEquals($expected, $actual);
        }
    }
}
