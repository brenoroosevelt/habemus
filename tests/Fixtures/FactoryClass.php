<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use stdClass;

class FactoryClass
{
    public function newObject($optionalValue = null): stdClass
    {
        $object = new stdClass();
        if ($optionalValue !== null) {
            $object->value = $optionalValue;
        }

        return $object;
    }

    public static function createObject(): stdClass
    {
        return new stdClass();
    }
}
