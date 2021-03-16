<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowiring\Attributes\Inject;

class PropertySelfCircularDependency
{
    // @codingStandardsIgnoreStart
    #[Inject(self::class)]
    protected $x;
    // @codingStandardsIgnoreEnd
}
