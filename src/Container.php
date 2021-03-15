<?php
declare(strict_types=1);

namespace Habemus;

use ArrayAccess;
use Habemus\Autowire\Attributes\AttributesInjection;
use Habemus\Autowire\ClassResolver;
use Habemus\Autowire\ReflectionClassResolver;
use Habemus\Autowire\Reflector;
use Habemus\Definition\AutoDetection;
use Habemus\Definition\Available\ClassDefinition;
use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionDetection;
use Habemus\Definition\DefinitionFactory;
use Habemus\Definition\DefinitionList;
use Habemus\Definition\DefinitionResolver;
use Habemus\Definition\DefinitionResolverInterface;
use Habemus\Definition\DefinitionWrapper;
use Habemus\Exception\NotFound;
use Habemus\ServiceProvider\ServiceProvider;
use Habemus\ServiceProvider\ServiceProviderManager;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface, ArrayAccess
{
    use DefinitionFactory;

    /**
     * @var CircularDependencyDetection
     */
    protected $circularDependencyDetection;

    /**
     * @var DefinitionList
     */
    protected $definitions;

    /**
     * @var ResolvedList
     */
    protected $resolved;

    /**
     * @var DefinitionDetection
     */
    protected $detection;

    /**
     * @var DefinitionResolverInterface
     */
    protected $definitionResolver;

    /**
     * @var ClassResolver
     */
    protected $classResolver;

    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * @var AttributesInjection
     */
    protected $attributesInjection;

    /**
     * @var ServiceProviderManager
     */
    protected $providers;

    /**
     * @var ContainerComposite
     */
    protected $delegates;

    /**
     * @var bool
     */
    protected $defaultShared;

    /**
     * @var bool
     */
    protected $useAutowire;

    /**
     * @var bool
     */
    protected $useAttributes;

    public function __construct()
    {
        $this->reflector = new Reflector();
        $this->useAutowire = true;
        $this->defaultShared = true;
        $this->useAttributes = $this->reflector->attributesAvailable();

        $this->circularDependencyDetection = new CircularDependencyDetection();
        $this->definitions = new DefinitionList();
        $this->resolved = new ResolvedList();
        $this->delegates = new ContainerComposite();
        $this->detection = new AutoDetection($this);
        $this->providers = new ServiceProviderManager($this);
        $this->attributesInjection = new AttributesInjection($this, $this->reflector);
        $this->classResolver = new ReflectionClassResolver($this, $this->attributesInjection, $this->reflector);
        $this->definitionResolver = new DefinitionResolver($this, $this->resolved, $this->attributesInjection);

        $this->add(ContainerInterface::class, new RawDefinition($this));
        $this->add(self::class, new RawDefinition($this));
    }

    public function add(string $id, $value): DefinitionWrapper
    {
        $definition = $this->detection->detect($value);
        $this->resolved->delete($id);
        $this->definitions->add($id, $definition);

        if ($definition instanceof RawDefinition) {
            $this->resolved->share($id, $definition->getValue());
        }

        if ($definition instanceof ClassDefinition) {
            $definition->setClassResolver($this->classResolver);
        }

        return new DefinitionWrapper($definition);
    }

    public function has($id): bool
    {
        return
            $this->resolved->has($id)           ||
            $this->definitions->has($id)        ||
            $this->definitions->hasTag($id)     ||
            $this->providers->provides($id)     ||
            $this->delegates->has($id)          ||
            $this->shouldAutowireResolve($id);
    }

    public function get($id)
    {
        if ($this->resolved->has($id)) {
            return $this->resolved->get($id);
        }

        return
            $this->circularDependencyDetection
                ->execute($id, function () use ($id) {
                    return $this->resolve($id);
                });
    }

    protected function resolve(string $id)
    {
        $this->providers->registerLazyProviderFor($id);

        if ($this->definitions->has($id)) {
            $definition = $this->definitions->get($id);
            return $this->definitionResolver->resolve($id, $definition);
        }

        if ($this->definitions->hasTag($id)) {
            $tagged = $this->definitions->getTagged($id);
            return $this->definitionResolver->resolveMany($tagged);
        }

        if ($this->shouldAutowireResolve($id)) {
            $definition =
                (new ClassDefinition($id))
                    ->setShared($this->defaultShared)
                    ->setClassResolver($this->classResolver);
            return $this->definitionResolver->resolve($id, $definition);
        }

        if ($this->delegates->has($id)) {
            return $this->delegates->get($id);
        }

        throw NotFound::noEntryWasFound($id);
    }

    public function injectDependency($object)
    {
        $this->attributesInjection->injectProperties($object);
    }

    public function addProvider(ServiceProvider ...$providers): self
    {
        $this->providers->add(...$providers);
        return $this;
    }

    public function addDelegate(ContainerInterface $container, ?int $priority = null): self
    {
        $this->delegates->add($container, $priority);
        return $this;
    }

    public function useDefaultShared(bool $share): self
    {
        $this->defaultShared = $share;
        return $this;
    }

    public function defaultShared(): bool
    {
        return $this->defaultShared;
    }

    public function autowireEnabled(): bool
    {
        return $this->useAutowire;
    }

    public function useAutowire(bool $enabled): self
    {
        $this->useAutowire = $enabled;
        return $this;
    }

    public function attributesEnabled(): bool
    {
        return $this->useAttributes;
    }

    public function useAttributes(bool $useAttributes): self
    {
        if ($useAttributes == true) {
            $this->reflector->assertAttributesAvailable();
        }

        $this->useAttributes = $useAttributes;
        return $this;
    }

    protected function shouldAutowireResolve($id): bool
    {
        return $this->useAutowire && class_exists($id);
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->definitions->delete($offset);
        $this->resolved->delete($offset);
    }
}
