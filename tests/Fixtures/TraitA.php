<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowiring\Attributes\Inject;

trait TraitA
{
    // @codingStandardsIgnoreStart
    #[Inject(ClassC::class)]
    protected $a;

    #[Inject()]
    protected ClassC $b;

    public function a()
    {
        return $this->a;
    }

    public function b()
    {
        return $this->b;
    }
    // @codingStandardsIgnoreEnd
}
