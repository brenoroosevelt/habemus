<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class DependencyClassB
{
    public function __construct(DependencyClassA $a)
    {
    }
}
