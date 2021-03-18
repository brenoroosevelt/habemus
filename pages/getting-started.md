---
layout: default
---
# Getting started

After installation, you need an instance of Habemus Container. Usually a single container is used for the whole application. It is often configured in your bootstrap script.

```php
<?php
use Habemus\Container;

$container = new Container();
```

## Registering services

You can register your services calling the `add(string $id, $value)` method:

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
