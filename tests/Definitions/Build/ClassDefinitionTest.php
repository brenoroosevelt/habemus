<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Build;

use Habemus\Autowiring\Attributes\AttributesInjection;
use Habemus\Autowiring\ClassResolver;
use Habemus\Autowiring\Parameter\ParameterResolverChain;
use Habemus\Autowiring\ReflectionClassResolver;
use Habemus\Autowiring\Reflector;
use Habemus\Container;
use Habemus\Definition\Build\ClassDefinition;
use Habemus\Exception\ContainerException;
use Habemus\Exception\NotFoundException;
use Habemus\Exception\UnresolvableParameterException;
use Habemus\Test\Fixtures\ClassB;
use Habemus\Test\Fixtures\ClassC;
use Habemus\Test\TestCase;
use RuntimeException;

class ClassDefinitionTest extends TestCase
{
    /**
     * @var ClassResolver
     */
    protected $classResolver;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjecton;

    /**
     * @var Reflector
     */
    protected $reflector;

    public function setUp(): void
    {
        $this->container = new Container();
        $this->reflector = new Reflector();
        $this->attributesInjecton = new AttributesInjection($this->container, $this->reflector);
        $this->classResolver =
            new ReflectionClassResolver(
                ParameterResolverChain::default($this->container, $this->attributesInjecton, $this->reflector)
            );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->definitionResolver);
        unset($this->attributesInjecton);
        unset($this->reflector);
        unset($this->resolvedList);
        unset($this->container);
        parent::tearDown();
    }

    public function testShouldCreateClassDefinitionDefaultConstructor()
    {
        $definition = new ClassDefinition(ClassB::class);
        $this->assertEmpty($definition->getConstructorParameters());
        $this->assertEquals(ClassB::class, $definition->class());
    }

    public function testShouldCreateClassDefinitionWithConstructorParameters()
    {
        $definition = new ClassDefinition(ClassB::class, ['param' => 'value']);
        $this->assertSame(['param' => 'value'], $definition->getConstructorParameters());
    }

    public function testShouldClassDefinitionAddConstructorParameters()
    {
        $definition = new ClassDefinition(ClassB::class);
        $definition->constructor('param', 'value')->constructor('param2', 'value2');
        $this->assertSame(['param' => 'value', 'param2' => 'value2'], $definition->getConstructorParameters());
    }

    public function testShouldClassDefinitionResolveInstance()
    {
        $this->container->useAutowire(true);
        $definition =
            (new ClassDefinition(ClassB::class, ['param' => 'value']))
                ->setClassResolver($this->classResolver);
        $instance = $definition->getConcrete($this->container);

        $this->assertInstanceOf(ClassB::class, $instance);
        $this->assertEquals('value', $instance->value);
    }

    public function testShouldNotClassDefinitionResolveInstanceWithoutParameters()
    {
        $this->container->useAutowire(true);
        $definition =
            (new ClassDefinition(ClassB::class))
                ->setClassResolver($this->classResolver);
        $this->expectException(UnresolvableParameterException::class);
        $definition->getConcrete($this->container);
    }

    public function testShouldNotClassDefinitionResolveAnUnknownClass()
    {
        $this->container->useAutowire(true);
        $definition =
            (new ClassDefinition('UnknownClass'))
                ->setClassResolver($this->classResolver);
        $this->expectException(NotFoundException::class);
        $definition->getConcrete($this->container);
    }

    public function testShouldNotClassDefinitionResolveWithoutClassResolver()
    {
        $definition = new ClassDefinition(ClassC::class);
        $this->expectException(ContainerException::class);
        $definition->getConcrete($this->container);
    }
}
