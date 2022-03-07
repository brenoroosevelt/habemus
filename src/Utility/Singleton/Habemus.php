<?php
declare(strict_types=1);

namespace Habemus\Utility\Singleton;

use Habemus\Container;

final class Habemus
{
    /** @var Container|null */
    private static $instance = null;

    public static function instance(): Container
    {
        return self::$instance ?? self::$instance = new Container();
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
