<?php
declare(strict_types=1);

namespace Habemus\ServiceProvider;

use Habemus\Container;

interface ServiceProvider
{
    public function register(Container $container): void;
}
