<?php
declare(strict_types=1);

namespace Habemus\Test\ServiceProvider;

use Habemus\Container;
use Habemus\ServiceProvider\LazyServiceProvider;
use Habemus\ServiceProvider\ServiceProvider;
use Habemus\ServiceProvider\ServiceProviderManager;
use Habemus\Test\TestCase;

class ServiceProviderManagerTest extends TestCase
{
    protected function newProvider(): ServiceProvider
    {
        return new class implements ServiceProvider {
            public function register(Container $container): void
            {
                $container->add("id1", "value1");
                $container->add("id2", "value2");
            }
        };
    }

    protected function newLazyProvider(): ServiceProvider
    {
        return new class implements LazyServiceProvider {
            public function provides(string $id): bool
            {
                return in_array($id, ["id1", "id2"]);
            }

            public function register(Container $container): void
            {
                $container->add("id1", "value1");
                $container->add("id2", "value2");
            }
        };
    }

    public function testShouldCreateEmptyServiceProviderManager()
    {
        $providers = new ServiceProviderManager(new Container());
        $this->assertEquals(0, $providers->count());
    }

    public function testShouldAddServiceProvider()
    {
        $providers = new ServiceProviderManager(new Container());
        $providers->add($this->newProvider());
        $this->assertEquals(1, $providers->count());
    }

    public function testShouldRegisterServicesProviderAutomatically()
    {
        $container = new Container();
        $providers = new ServiceProviderManager($container);
        $this->assertFalse($container->has('id1'));
        $providers->add($this->newProvider());
        $this->assertTrue($container->has('id1'));
    }

    public function testShouldNotRegisterLazyServicesProviderAutomatically()
    {
        $container = new Container();
        $providers = new ServiceProviderManager($container);
        $this->assertFalse($container->has('id1'));
        $providers->add($this->newLazyProvider());
        $this->assertFalse($container->has('id1'));
    }

    public function testShouldCheckForServicesInLazyServiceProvider()
    {
        $providers = new ServiceProviderManager(new Container());
        $providers->add($this->newLazyProvider());
        $this->assertTrue($providers->provides('id1'));
        $this->assertFalse($providers->provides('id3'));
    }

    public function testShouldNotRegisterServiceProviderTwice()
    {
        $providers = new ServiceProviderManager(new Container());
        $provider = $this->newProvider();
        $providers->add($provider);
        $providers->add($provider);
        $providers->add($provider);
        $this->assertEquals(1, $providers->count());
    }

    public function testShouldRegisterLazyServiceProviderByServiceId()
    {
        $container = new Container();
        $providers = new ServiceProviderManager($container);
        $providers->add($this->newLazyProvider());
        $this->assertFalse($container->has('id1'));
        $providers->registerLazyProviderFor('id1');
        $this->assertTrue($container->has('id1'));
    }
}
