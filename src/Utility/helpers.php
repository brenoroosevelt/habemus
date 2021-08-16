<?php
declare(strict_types=1);

namespace Habemus\Utility;

use Habemus\Container;
use Habemus\Utility\Singleton\Habemus;

if (! function_exists('Habemus\Utility\container')) {
    function container(): Container
    {
        return Habemus::instance();
    }
}

if (! function_exists('Habemus\Utility\injectDependency')) {
    function injectDependency($object): void
    {
        Habemus::instance()->injectDependency($object);
    }
}

if (! function_exists('Habemus\Utility\invoke')) {
    function invoke($target, array $args = [])
    {
        return Habemus::instance()->invoke($target, $args);
    }
}
