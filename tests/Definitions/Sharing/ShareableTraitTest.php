<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Sharing;

use Habemus\Definition\Sharing\Shareable;
use Habemus\Definition\Sharing\ShareableTrait;
use Habemus\Test\TestCase;

class ShareableTraitTest extends TestCase
{
    protected function newTraitInstance()
    {
        return new class {
            use ShareableTrait;
        };
    }

    public function testShouldTraitImplementsShareable()
    {
        $trait = new class implements Shareable {
            use ShareableTrait;
        };
        $this->assertInstanceOf(Shareable::class, $trait);
    }

    public function testShouldShareableTraitSharedByDefault()
    {
        $trait = $this->newTraitInstance();
        $this->assertTrue($trait->isShared());
    }

    public function testShouldShareableTraitSetShared()
    {
        $trait = $this->newTraitInstance();
        $trait->setShared(true);
        $this->assertTrue($trait->isShared());
    }

    public function testShouldShareableTraitSetNotShared()
    {
        $trait = $this->newTraitInstance();
        $trait->setShared(false);
        $this->assertFalse($trait->isShared());
    }
}
