<?php
declare(strict_types=1);

namespace Habemus\Test\Autowire\Attribute;

use Habemus\Autowiring\Attributes\Inject;
use Habemus\Test\TestCase;

class InjectTest extends TestCase
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
