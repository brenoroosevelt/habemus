#Installation

## Requirements

You need PHP >= 7.1 to use Habemus Container.

### Composer

Composer is the recommended method of installing Habemus. Make sure you have the current version of Composer and import the Habemus package for your project.

```shell
composer require brenoroosevelt/habemus
```

Then, don't forget to import Composer autoloader into your bootstrap script:

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

## 

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