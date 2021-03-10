<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class ClassA
{
    public $value = 0;

    public function method()
    {
        $this->value = 1;
    }
}
