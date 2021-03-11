<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class ClassB
{
    public $value;

    public function __construct(string $param)
    {
        $this->value = $param;
    }

    public function method()
    {
        $this->value = 1;
    }
}
