# Habemus
[![Build](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml/badge.svg)](https://github.com/brenoroosevelt/habemus/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/brenoroosevelt/habemus/branch/main/graph/badge.svg?token=S1QBA18IBX)](https://codecov.io/gh/brenoroosevelt/habemus)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brenoroosevelt/habemus/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/brenoroosevelt/habemus/?branch=main)
[![Latest Version](https://img.shields.io/github/release/brenoroosevelt/habemus.svg?style=flat)](https://github.com/brenoroosevelt/habemus/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)

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

## Requirements

This package supports the following versions of PHP:

* PHP 7.1
* PHP 7.2
* PHP 7.3
* PHP 7.4
* PHP 8.0


## Install

Via Composer

``` bash
$ composer require brenoroosevelt/habemus
```

## Documentation

Read the [full documentation](http://brenoroosevelt.github.io/habemus).

## Contributing

Please read the [Contributing](CONTRIBUTING.md) guide to learn about contributing to this project.

## License

This project is licensed under the terms of the MIT license. See the [LICENSE](LICENSE.md) file for license rights and limitations.
