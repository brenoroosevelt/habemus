# Habemus
[![Build](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml/badge.svg)](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/brenoroosevelt/habemus/branch/main/graph/badge.svg?token=S1QBA18IBX)](https://codecov.io/gh/brenoroosevelt/habemus)

Habemus is a PSR-11 compatible dependency injection container. This package provides autowiring to implement Inversion of Control (IoC) containers for PHP.

## Features

Habemus supports:

- [PSR-11](http://www.php-fig.org/psr/psr-11/) compatible.
- Auto wiring (recursively through all dependencies)
- Constructor injection.
- Setter injection.
- Property injection using PHP 8.0 Attributes.
- Circular dependency detection.
- Interfaces, Factories, Closures, Callbacks.
- Delegated containers (container composite).
- Service providers and lazy service providers.
- Taggable services.
- Shareable instances.
- Variadic arguments in the constructor.
- Aliasing.


## Requirements

* PHP >=7.1


## Install

Via Composer

``` bash
$ composer require brenoroosevelt/habemus
```

## Usage

```php
use Habemus\Container;

$container = new Container();

$foo = $container->get(Foo::class);
```

## Documentation

Read the [full documentation](http://brenoroosevelt.github.io/habemus).

## Contributing

Please read the Contributing guide to learn about contributing to this project. 