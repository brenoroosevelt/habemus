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

    #[Inject(ClassA::class)]
    private $h;

    #[Inject]
    private ClassA $i;

    public function __construct(#[Inject('id1')] $a, #[Inject] ClassA $classA, #[Inject('id1')] string ...$str)
    {
    }

    public function a()
    {
        return $this->a;
    }

    public function b()
    {
        return $this->b;
    }

    public function c()
    {
        return $this->c;
    }

    public function d()
    {
        return $this->d;
    }

    public function e()
    {
        return $this->e;
    }

    public function f()
    {
        return $this->f;
    }

    public function g(): ClassA
    {
        return $this->g;
    }

    public function h(): ClassB
    {
        return $this->h;
    }

    public function i(): ClassA
    {
        return $this->i;
    }
}
