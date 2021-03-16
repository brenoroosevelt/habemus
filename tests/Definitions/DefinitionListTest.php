<?php
declare(strict_types=1);

namespace Habemus\Test\Definitions;

use Habemus\Definition\Available\RawDefinition;
use Habemus\Definition\DefinitionList;
use Habemus\Test\TestCase;

class DefinitionListTest extends TestCase
{
    public function testShouldCreateAnEmptyDefinitionList()
    {
        $definitionList = new DefinitionList();
        $this->assertTrue($definitionList->isEmpty());
    }

    public function testShouldAddGetAnItemDefinitionList()
    {
        $definitionList = new DefinitionList();
        $item = (new RawDefinition("value"))->setIdentity('id1');
        $definitionList->add($item);

        $this->assertEquals(1, $definitionList->count());
        $this->assertEquals($item, $definitionList->get('id1'));
    }

    public function testShouldCheckForElementsOnTheDefinitionList()
    {
        $definitionList = new DefinitionList();
        $item = (new RawDefinition("value"))->setIdentity('id1');
        $definitionList->add($item);

        $this->assertTrue($definitionList->has('id1'));
    }

    public function testShouldDeleteAnItemFromDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add((new RawDefinition("value"))->setIdentity('id1'));
        $definitionList->delete('id1');

        $this->assertEquals(0, $definitionList->count());
    }

    public function testShouldCountElementsDefinitionList()
    {
        $definitionList = new DefinitionList();
        $this->assertEquals(0, $definitionList->count());
        $this->assertTrue($definitionList->isEmpty());

        $definitionList->add((new RawDefinition(1))->setIdentity('id1'));
        $definitionList->add((new RawDefinition(2))->setIdentity('id2'));
        $definitionList->add((new RawDefinition(3))->setIdentity('id3'));

        $this->assertEquals(3, $definitionList->count());
        $this->assertFalse($definitionList->isEmpty());
    }

    public function testShouldReplaceElementsByIdOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $lastDefinition = (new RawDefinition(20))->setIdentity('id1');
        $definitionList->add((new RawDefinition(10))->setIdentity('id1'));
        $definitionList->add($lastDefinition);

        $this->assertEquals($lastDefinition, $definitionList->get('id1'));
    }

    public function testShouldCheckForElementsOnDefinitionListAfterDelete()
    {
        $definitionList = new DefinitionList();
        $definitionList->add((new RawDefinition(null))->setIdentity('id1'));
        $definitionList->delete('id1');
        $this->assertFalse($definitionList->has('id1'));
    }

    public function testShouldIterateAsArrayElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add($v1 = (new RawDefinition("value1"))->setIdentity('id1'));
        $definitionList->add($v2 = (new RawDefinition("value2"))->setIdentity('id2'));
        $definitionList->add($v3 = (new RawDefinition("value3"))->setIdentity('id3'));
        $definitionList->add($v4 = (new RawDefinition("value4"))->setIdentity('id4'));

        $items = [];
        foreach ($definitionList as $value) {
            $items[] = $value;
        }

        $this->assertEquals([
            $v1,
            $v2,
            $v3,
            $v4,
        ], $items);
    }

    public function testShouldIterateElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add($v1 = (new RawDefinition("value1"))->setIdentity('id1'));
        $definitionList->add($v2 = (new RawDefinition("value2"))->setIdentity('id2'));

        $items = [];
        foreach ($definitionList->getIterator() as $value) {
            $items[] = $value;
        }

        $this->assertEquals([
            $v1,
            $v2,
        ], $items);
    }

    public function testShouldGetTaggedElementsFromDefinitionList()
    {
        $definitionList = new DefinitionList();
        $def1 = (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2')->setIdentity('id1');
        $definitionList->add($def1);
        $definitionList->add($def2 = (new RawDefinition("value2"))->addTag('tag2')->setIdentity('id2'));
        $definitionList->add($def3 = (new RawDefinition("value3"))->addTag('tag1')->setIdentity('id3'));
        $definitionList->add($def4 = (new RawDefinition("value4"))->addTag('tag3')->setIdentity('id4'));
        $definitionList->add($def5 = (new RawDefinition("value5"))->setIdentity('id5'));

        $tag1 = $definitionList->getTagged('tag1');
        $tag2 = $definitionList->getTagged('tag2');
        $tag3 = $definitionList->getTagged('tag3');

        $this->assertCount(2, $tag1);
        $this->assertCount(2, $tag2);
        $this->assertCount(1, $tag3);

        foreach ($tag1 as $definition) {
            $this->assertTrue(in_array($definition, [$def1, $def3], true));
        }
    }

    public function testShouldCheckTaggedElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add(
            (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2')->setIdentity('id1')
        );
        $definitionList->add((new RawDefinition("value2"))->addTag('tag2')->setIdentity('id2'));
        $definitionList->add((new RawDefinition("value3"))->addTag('tag1')->setIdentity('id3'));
        $definitionList->add((new RawDefinition("value4"))->addTag('tag3')->setIdentity('id4'));
        $definitionList->add((new RawDefinition("value5"))->setIdentity('id5'));

        $this->assertTrue($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag2'));
        $this->assertTrue($definitionList->hasTag('tag3'));
        $this->assertFalse($definitionList->hasTag('tag4'));
    }

    public function testShouldCheckTaggedElementsOnDefinitionListAfterReplace()
    {
        $definitionList = new DefinitionList();
        $definitionList->add(
            (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2')->setIdentity('id1')
        );
        $definitionList->add(
            (new RawDefinition("value1"))->addTag('tag1')->addTag('tag3')->setIdentity('id2')
        );
        $definitionList->add(
            (new RawDefinition("value1"))->setIdentity('id1')
        ); // replace definition 'id1'

        $this->assertTrue($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag3'));
        $this->assertFalse($definitionList->hasTag('tag2'));
    }

    public function testShouldCheckTaggedElementsOnDefinitionListAfterDelete()
    {
        $definitionList = new DefinitionList();
        $definitionList->add(
            (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2')->setIdentity('id1')
        );
        $definitionList->add(
            (new RawDefinition("value1"))->addTag('tag2')->addTag('tag3')->setIdentity('id2')
        );
        $definitionList->delete('id1');

        $this->assertFalse($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag2'));
        $this->assertTrue($definitionList->hasTag('tag3'));
    }
}
