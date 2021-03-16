<?php
declare(strict_types=1);

namespace Habemus\Utility;

abstract class PHPVersion
{
    const V5_3 = 50300;
    const V5_4 = 50400;
    const V5_6 = 50600;
    const V7_0 = 70000;
    const V7_1 = 70100;
    const V7_2 = 70200;
    const V7_3 = 70300;
    const V7_4 = 70400;
    const V8_0 = 80000;

    public static function current(): int
    {
        return PHP_VERSION_ID;
    }
}
