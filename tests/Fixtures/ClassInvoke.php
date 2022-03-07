<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowiring\Attributes\Inject;

class ClassInvoke
{
    public function __invoke(ClassC $c, int $a)
    {
        return $a;
    }

    // phpcs:disable
    public function method(
        #[Inject('property_id')]
        int $p,
        ClassC $c
    ) {
        return 10;
    }
    // phpcs:enable
}
