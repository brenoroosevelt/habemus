<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowire\Attributes\Inject;

trait TraitA
{
    #[Inject(ClassA::class)]
    protected $a;

    #[Inject()]
    protected ClassA $b;

    public function a()
    {
        return $this->a;
    }

    public function b()
    {
        return $this->b;
    }
}
