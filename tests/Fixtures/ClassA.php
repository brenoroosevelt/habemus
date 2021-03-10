<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowire\Attributes\Inject;

class ClassA
{
    public $value;

    #[Inject('property_id')]
    protected $property;

    public function __construct()
    {
        $this->property = null;
        $this->value = 0;
    }

    public function method()
    {
        $this->value = 1;
    }

    public function property()
    {
        return $this->property;
    }
}
