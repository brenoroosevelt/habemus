<?php
declare(strict_types=1);

namespace Habemus\ServiceProvider;

use Habemus\Container;
use Habemus\ServiceProvider\LazyServiceProvider;
use Habemus\ServiceProvider\ServiceProvider;
use SplObjectStorage;

class ServiceProviderManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var SplObjectStorage
     */
    protected $providers;

    /**
     * @var SplObjectStorage
     */
    protected $lazyProviders;

    public function __construct(Container $container, ServiceProvider ...$providers)
    {
        $this->lazyProviders = new SplObjectStorage();
        $this->providers = new SplObjectStorage();
        $this->container = $container;
        $this->add(...$providers);
    }

    public function add(ServiceProvider ...$providers)
    {
        foreach ($providers as $provider) {
            if ($this->providers->contains($provider)) {
                continue;
            }

            $this->providers->attach($provider);
            if ($provider instanceof LazyServiceProvider) {
                $this->lazyProviders->attach($provider);
            } else {
                $provider->register($this->container);
            }
        }
    }

    public function registerLazyProviderFor(string $id)
    {
        foreach ($this->lazyProviders as $provider) {
            if ($provider->provides($id)) {
                $provider->register($this->container);
                $this->lazyProviders->detach($provider);
                break;
            }
        }
    }

    public function provides(string $id): bool
    {
        foreach ($this->lazyProviders as $provider) {
            if ($provider->provides($id)) {
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->providers);
    }
}
