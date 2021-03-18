---
layout: home
---

# Habemus Container
[![Build](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml/badge.svg)](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/brenoroosevelt/habemus/branch/main/graph/badge.svg?token=S1QBA18IBX)](https://codecov.io/gh/brenoroosevelt/habemus)

Habemus is a PSR-11 compatible dependency injection container. This package provides autowiring to implement Inversion of Control (IoC) containers for PHP.

## Features

Habemus supports:

- [PSR-11](http://www.php-fig.org/psr/psr-11/) compatible.
- Auto wiring (recursively through all dependencies)
- Constructor injection.
- Setter injection.
- Property/constructor injection using PHP 8 Attributes.
- Circular dependency detection.
- Interfaces, Factories, Closures, Callbacks.
- Delegated containers.
- Container composite.
- Service providers and lazy service providers.
- Taggable services.
- Shareable instances.
- Variadic arguments in the constructor.
- Aliasing.
- Array Access.



# Installation

## Requirements

You need PHP >= 7.1 to use Habemus Container.

### Composer

Make sure you have the current version of Composer and then import the Habemus package for your project.

```shell
composer require brenoroosevelt/habemus
```

Import Composer autoloader in your bootstrap script:

```php
<?php
require 'vendor/autoload.php';
```

# Getting started

After installation, you need an instance of Habemus Container. Usually a single container is used for the whole application. It is often configured in your bootstrap script.

```php
<?php
use Habemus\Container;

$container = new Container();
```

## Registering services

Containers are typically implementations of the Service Locator pattern. To register your services just call the `add(string $id, $value)` method, like this:

```php
<?php

// Interface and its implementation (instances will be resolved by container)
$container->add(UserRepositoryInterface::class, RedisUserRepository::class);
// Some (resolved) service 
$container->add('SomeService', new SomeService());
// Numbers, strings, arrays
$container->add('my_secret_number', 123);
$container->add('my_string', "Hi, I'm using Habemus Container");
$container->add('settings', ['my_config' => 'value']);
// Closure factory
$container->add('stdCreator', fn() => new stdClass());
```

## Using Container

Habemus is a PSR-11 compliant implementation. So, to get a service, just use the `get(string $id): mixed` method. You can check services by calling the `has(string $id): bool` method to avoid a NotFoundException.

```php
<?php

$repository = $container->get(UserRepositoryInterface::class);
$settings = $container->get('settings');

// checking for services
if ($container->has('stdCreator')) {
    $newStd = $container->get('stdCreator');
}
```
## Auto wiring

Auto wiring is enabled by default and you only need to ask for an instance.

```php
<?php

class Foo {}

class Bar
{
    public function __construct(Foo $foo) {}
}
```

```php
<?php
// No registration required; no configuration:
$bar = $container->get(Bar::class); 
var_dump($bar instanceof Bar); // true
```
When `Auto wiring` is enabled, Habemus Container is able to resolve instances of objects and their dependencies by inspecting type hints in the constructors. No configuration is required.

# Dependency Injection

Habemus is more than an implementation of Service Locator pattern, and we can go further. So what if you need to decouple your class dependencies and inject them where they are needed? Don't worry, Habemus got you covered!

## Constructor Injection

Consider the scenario below:

```php
<?php

interface FooInterface {}

class SimpleFoo implements FooInterface {}

class SpecialFoo implements FooInterface {}

class Bar {}

class MyClass
{
    public $foo;
    public $bar;
    
    public function __construct(FooInterface $foo, Bar $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
```

When is dealing with an interface, the container is unable to resolve the dependency. In the example below, the container does not know how to resolve an instance of FooInterface and will throw an exception.

```php
<?php

$container->get(FooInterface::class); // NotFoundException
$container->get(MyClass::class);  // UnresolvableParameterException
```
In this case, you need specify how container will resolve instances of FooInterface: 

```php
<?php
// contanier will try to resolve with an instance of SimpleFoo
$container->add(FooInterface::class, SimpleFoo::class);
// or you can use a specify instance:
$container->add(FooInterface::class, new SimpleFoo() );
// or a factory
$container->add(FooInterface::class, fn() => new SimpleFoo()); 

$foo = $container->get(FooInterface::class);
var_dump($foo instanceof FooInterface); // true
var_dump($foo instanceof SimpleFoo); // true

$myClass = $container->get(MyClass::class);
var_dump($myClass->foo instanceof SimpleFoo); // true
var_dump($myClass->foo instanceof SpecialFoo); // false
```
Now, container will always resolve FooInterface instances with a SimpleFoo object, but you can use a specific instance in a particular class:
```php
<?php

// a specific instance for MyClass:
$container->add(MyClass::class)
    ->constructor('foo', new SpecialFoo());
// or a reference to another service:
$container->add(MyClass::class)
    ->constructor('foo', Container::use(SpecialFoo::class));

$myClass = $container->get(MyClass::class);
var_dump($myClass->foo instanceof SimpleFoo); // false
var_dump($myClass->foo instanceof SpecialFoo); // true

$foo = $container->get(FooInterface::class);
var_dump($foo instanceof SimpleFoo); // true
```

As with interfaces, the container cannot resolve primitive types in the constructor.

```php
<?php
class MyClass
{
    public function __construct(int $min, int $max) {}
}
```

```php
<?php

$container->get(MyClass::class); // UnresolvableParameterException
```

In this case, you need to specify constructor parameters for primitive types:

```php
<?php

$container->add(MyClass::class)
    ->constructor('min', 1)
    ->constructor('max', 50);
// or a reference to another services in the container:
$container->add(MyClass::class)
    ->constructor('min', Container::use('config_min'))
    ->constructor('max', Container::use('config_max'));
```

### Constructor Injection with Attributes

You can use PHP 8 Attributes to inject dependencies in constructor parameters. See how simple it is:
```php
<?php

$container->add('config_min', 1);
$container->add('config_max', 50);
```
```php
class MyClass
{
    public function __construct(
        #[Inject(SimpleFoo::class)]
        public FooInterface $foo, 
        #[Inject('config_min')]
        public int $min, 
        #[Inject('config_max')]
        public int $max
    ) { }
}
```
All constructor dependencies will be resolved by the container.

```php
<?php
// 
$myClass = $container->get(MyClass::class);
var_dump($myClass->foo instanceof SimpleFoo); // true
var_dump($myClass->min); // 1
var_dump($myClass->max); // 50
```
## Property Injection

Habemus property injection use PHP 8 Attributes. You only need to pass the service identification as a parameter of the `Inject` attribute. If identification is left empty, the container will attempt to use the type hint. 

```php
<?php

$container->add('config_min', 1);
```
```php
class MyClass
{
    #[Inject(SimpleFoo::class)]
    protected $foo;
    
    #[Inject]
    protected SpecialFoo $specialFoo; // use type hint
    
    #[Inject('config_min')]
    private $min;
    
    public function getSpecialFoo()
    {
        return $this->specialFoo;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
    
    public function getMin()
    {
        return $this->min;
    }
}
```

```php
<?php

$myClass = $container->get(MyClass::class);
var_dump($myClass->getFoo() instanceof SimpleFoo); // true
var_dump($myClass->getSpecialFoo() instanceof SpecialFoo); // true
var_dump($myClass->getMin()); // 1
```

## Setter Injection

Habemus allows you to use setter injection.

```php

class Foo {}

class MyClass
{
    protected $foo;
    
    public function setFoo(Foo $foo)
    {
        $this->foo = $foo;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
}
```
```php
<?php
// configuring setter method
$container->add(MyClass::class)
    ->addMethodCall('setFoo', [new SimpleFoo()]);
// configuring setter method, using a reference (parameter):
$container->add(MyClass::class)
    ->addMethodCall('setFoo', [Container::use(SimpleFoo::class)]);
    
$myClass = $container->get(MyClass::class);
var_dump($myClass->getFoo() instanceof SimpleFoo); // true
```
The container will resolve an instance of MyClass and then call the setter method.

