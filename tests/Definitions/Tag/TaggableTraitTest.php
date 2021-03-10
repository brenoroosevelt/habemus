<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions\Tag;

use Habemus\Definition\Tag\Taggable;
use Habemus\Definition\Tag\TaggableTrait;
use Habemus\Test\TestCase;

class TaggableTraitTest extends TestCase
{
    public function newTraitInstance()
    {
        return new class {
            use TaggableTrait;
        };
    }

    public function testShouldTagTraitImplementsTaggable()
    {
        $object = new class implements Taggable {
            use TaggableTrait;
        };
        $this->assertInstanceOf(Taggable::class, $object);
    }

    public function testShouldAddTag()
    {
        $object = $this->newTraitInstance();
        $object->addTag('tag1');
        $this->assertTrue($object->hasTag('tag1'));
        $this->assertCount(1, $object->getTags());
    }

    public function testShouldAddManyTags()
    {
        $object = $this->newTraitInstance();
        $object->addTag('tag1')->addTag('tag2')->addTag('tag3');
        $this->assertTrue($object->hasTag('tag1'));
        $this->assertTrue($object->hasTag('tag2'));
        $this->assertTrue($object->hasTag('tag3'));
        $this->assertCount(3, $object->getTags());
    }
}
