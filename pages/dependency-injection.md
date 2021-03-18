---
layout: default
---
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

