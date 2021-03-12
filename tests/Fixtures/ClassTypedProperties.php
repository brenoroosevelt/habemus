<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

class ClassTypedProperties
{
    public $a;
    public int $b;
    public float $c;
    public ClassA $d;
    public array $e = [];
    public ?ClassB $f;
    public GenericInterface $g;
    public AbstractClass $h;
    public self $i;
    public ?self $j;
}
