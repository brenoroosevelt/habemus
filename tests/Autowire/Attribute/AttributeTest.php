<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire\Attribute;

use Habemus\Autowire\Attributes\Inject;
use Habemus\Test\TestCase;

class AttributeTest extends TestCase
{
    public function testShouldCreateEmptyInjectAttribute()
    {
        $inject = new Inject();
        $this->assertNull($inject->getId());
    }

    public function testShouldCreateWithAnId()
    {
        $inject = new Inject('anId');
        $this->assertEquals('anId', $inject->getId());
    }
}
