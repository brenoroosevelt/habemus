# Installation

## Requirements

You need PHP >= 7.1 to use Habemus Container.

### Composer

Composer is the recommended way of installing Habemus. Make sure you have the current version of Composer and then import the Habemus package for your project.

```shell
composer require brenoroosevelt/habemus
```

Don't forget to import Composer autoloader in your bootstrap script:

```php
<?php
require 'vendor/autoload.php';
```

# Getting started

After installation, you need an instance of Habemus Container. Usually a single container is used for the whole application. It is often configured in the application's bootstrap script.

```php
<?php
use Habemus\Container;

$container = new Container();
```

## Registering services

Containers are typically implementations of the Service Locator pattern. You can register anything into the container: services, references, arrays, settings, strings, numbers, etc. To register your services just call the method `add(string $id, $value)`, like this:

```php
<?php

// Interface and its implementation (instances will be resolved by container)
$container->add(UserRepositoryInterface::class, RedisUserRepository::class);

// Some (resolved) service 
$container->add('SomeService', new SomeService());

// Numbers
$container->add('my_secret_number', 123);

// Strings
$container->add('my_string', "Hi, I'm using Habemus Container");

// Array settings
$container->add('settings', [
    'my_config' => 'value'
]);

// Closure factory
$container->add('stdCreator', fn() => new stdClass());
```

## Requesting services

Habemus is a PSR-11 compliant implementation. Therefore, to request container services, simply use the `get(string $id): mixed` method. You can check services by calling the `has(string $id): bool` method to avoid a NotFoundException.

```php
<?php

$repository = $container->get(UserRepositoryInterface::class);
$settings = $container->get('settings');

// checking for services
if ($container->has('stdCreator')) {
    $newStd = $container->get('stdCreator');
}
```

Ok, everything seems very simple so far... we only have three methods (`add` `has` `get`) and a standard behavior of Service Locator pattern.
Habemus is more than this, and we can go further. So what if you need to decouple your class dependencies and inject them where they are needed? Don't worry, Habemus got you covered!


## Interfaces and Abstract Classes

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

In the example below, the container does not know how to resolve an instance of FooInterface and will throw an exception.

```php
<?php

// Both will throw a NotFoundException
// "No entry was found for id (FooInterface)"
$container->get(FooInterface::class);
$container->get(MyClass::class); 
```
You need specify how container will resolve instances of FooInterface: 

```php
<?php

$container->add(FooInterface::class, SimpleFoo::class);

$foo = $container->get(FooInterface::class);
var_dump($foo instanceof FooInterface); // true
var_dump($foo instanceof SimpleFoo); // true

$myClass = $container->get(MyClass::class);
var_dump($myClass->foo instanceof SimpleFoo); // true
var_dump($myClass->foo instanceof SpecialFoo); // false
```
You can be more specific in certain cases:

```php
<?php

$container->add(MyClass::class, MyClass::class)->constructor('foo', SpecialFoo::class);

$myClass = $container->get(MyClass::class);
var_dump($myClass->foo instanceof SimpleFoo); // false
var_dump($myClass->foo instanceof SpecialFoo); // true
```

## Container options

* By setting `$container->useAutowire(true|false)`, 
* By setting `$container->useAttributes(true|false)`, ...
* By setting `$container->useDefaultShared(true|false)`, ...

## Welcome to GitHub Pages

You can use the [editor on GitHub](https://github.com/brenoroosevelt/habemus/edit/gh-pages/index.md) to maintain and preview the content for your website in Markdown files.

Whenever you commit to this repository, GitHub Pages will run [Jekyll](https://jekyllrb.com/) to rebuild the pages in your site, from the content in your Markdown files.

### Markdown

Markdown is a lightweight and easy-to-use syntax for styling your writing. It includes conventions for

```markdown
Syntax highlighted code block

# Header 1
## Header 2
### Header 3

- Bulleted
- List

1. Numbered
2. List

**Bold** and _Italic_ and `Code` text

[Link](url) and ![Image](src)
```

For more details see [GitHub Flavored Markdown](https://guides.github.com/features/mastering-markdown/).

### Jekyll Themes

Your Pages site will use the layout and styles from the Jekyll theme you have selected in your [repository settings](https://github.com/brenoroosevelt/habemus/settings). The name of this theme is saved in the Jekyll `_config.yml` configuration file.

### Support or Contact

Having trouble with Pages? Check out our [documentation](https://docs.github.com/categories/github-pages-basics/) or [contact support](https://support.github.com/contact) and we’ll help you sort it out.
