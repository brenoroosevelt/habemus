<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Identifiable;

use Habemus\Definition\Identifiable\Identifiable;
use Habemus\Definition\Identifiable\IdentifiableTrait;
use Habemus\Test\TestCase;

class IdentifiableTraitTest extends TestCase
{
    public function testShouldIdentifiableTraitImplementIdentifiableInterface()
    {
        $trait = new class implements Identifiable {
            use IdentifiableTrait;
        };
        $this->assertInstanceOf(Identifiable::class, $trait);
    }

    public function testShouldIdentifiableTraitSetGetId()
    {
        $trait = new class implements Identifiable {
            use IdentifiableTrait;
        };
        $trait->setIdentity('id1');
        $this->assertEquals('id1', $trait->getIdentity());
    }
}
