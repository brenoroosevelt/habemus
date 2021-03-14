<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class DependencyClassA
{
    public function __construct(DependencyClassB $b)
    {
    }
}
