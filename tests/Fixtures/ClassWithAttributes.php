<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowire\Attributes\Inject;

class ClassWithAttributes
{
    #[Inject('id1')]
    public $a;

    #[Inject('id1')]
    protected $b;

    #[Inject('id1')]
    private $c;

    #[Inject]
    public $d;

    #[Inject]
    protected $e;

    #[Inject]
    private $f;

    #[Inject(ClassA::class)]
    private ClassA $g;

    public function __construct(#[Inject('id1')] $a, #[Inject] ClassA $classA, #[Inject('id')] string ...$str)
    {
    }
}
