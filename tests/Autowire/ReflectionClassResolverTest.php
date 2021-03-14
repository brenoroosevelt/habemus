<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire;

use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\ReflectionClassResolver;
use Habemus\Autowire\Reflector;
use Habemus\Container;
use Habemus\Exception\NotFound;
use Habemus\Exception\NotInstatiable;
use Habemus\Test\Fixtures\AbstractClass;
use Habemus\Test\Fixtures\ClassA;
use Habemus\Test\Fixtures\ClassWithoutConstructor;
use Habemus\Test\Fixtures\GenericInterface;
use Habemus\Test\Fixtures\PrivateConstructor;
use Habemus\Test\TestCase;

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
            new ReflectionClassResolver($this->container, $this->attributesInjection, $this->reflector);
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
        $this->expectException(NotFound::class);
        $this->classResolver->resolveClass('UnknownClass');
    }

    public function testShouldGetErrorIfTryInstantiateInterface()
    {
        $this->expectException(NotFound::class);
        $this->classResolver->resolveClass(GenericInterface::class);
    }

    public function testShouldGetErrorIfTryInstantiateAbstractClass()
    {
        $this->expectException(NotInstatiable::class);
        $this->classResolver->resolveClass(AbstractClass::class);
    }

    public function testShouldGetErrorIfTryInstantiatePrivateConstructor()
    {
        $this->expectException(NotInstatiable::class);
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
}
