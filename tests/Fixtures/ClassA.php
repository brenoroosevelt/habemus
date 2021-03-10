<?php
declare(strict_types=1);

namespace Habemus\Test\Fixtures;

use Habemus\Autowire\Attributes\Inject;

class ClassA
{
    public $value = 0;

    #[Inject('property_id')]
    protected $property = null;

    public function method()
    {
        $this->value = 1;
    }

    public function property()
    {
        return $this->property;
    }
}
