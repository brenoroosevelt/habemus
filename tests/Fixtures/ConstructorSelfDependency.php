<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class ConstructorSelfDependency
{
    public function __construct(self $a)
    {
    }
}
