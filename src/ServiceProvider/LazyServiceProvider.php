<?php
declare(strict_types=1);

namespace Habemus\ServiceProvider;

use Habemus\ServiceProvider\ServiceProvider;

interface LazyServiceProvider extends ServiceProvider
{
    public function provides(string $id): bool;
}
