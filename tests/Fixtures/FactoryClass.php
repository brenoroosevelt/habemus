<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use stdClass;

class FactoryClass
{
    public function newObject(): stdClass
    {
        return new stdClass();
    }
}
