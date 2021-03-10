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

    public function testShouldAddGetAnItemToDefinitionList()
    {
        $definitionList = new DefinitionList();
        $item = new RawDefinition("value");
        $definitionList->add('id1', $item);

        $this->assertEquals(1, $definitionList->count());
        $this->assertEquals($item, $definitionList->get('id1'));
    }

    public function testShouldCheckForElementsOnTheDefinitionList()
    {
        $definitionList = new DefinitionList();
        $item = new RawDefinition("value");
        $definitionList->add('id1', $item);

        $this->assertTrue($definitionList->has('id1'));
    }

    public function testShouldDeleteAnItemFromDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', new RawDefinition("value"));
        $definitionList->delete('id1');

        $this->assertEquals(0, $definitionList->count());
    }

    public function testShouldCountElementsDefinitionList()
    {
        $definitionList = new DefinitionList();
        $this->assertEquals(0, $definitionList->count());
        $this->assertTrue($definitionList->isEmpty());

        $definitionList->add('id1', new RawDefinition(1));
        $definitionList->add('id2', new RawDefinition(2));
        $definitionList->add('id3', new RawDefinition(3));

        $this->assertEquals(3, $definitionList->count());
        $this->assertFalse($definitionList->isEmpty());
    }

    public function testShouldReplaceElementsByIdOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $lastDefinition = new RawDefinition(20);
        $definitionList->add('id1', new RawDefinition(10));
        $definitionList->add('id1', $lastDefinition);

        $this->assertEquals($lastDefinition, $definitionList->get('id1'));
    }

    public function testShouldCheckForElementsOnDefinitionListAfterDelete()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', new RawDefinition(null));
        $definitionList->delete('id1');
        $this->assertFalse($definitionList->has('id1'));
    }

    public function testShouldIterateAsArrayElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', $v1 = new RawDefinition("value1"));
        $definitionList->add('id2', $v2 = new RawDefinition("value2"));
        $definitionList->add('id3', $v3 = new RawDefinition("value3"));
        $definitionList->add('id4', $v4 = new RawDefinition("value4"));

        $items = [];
        foreach ($definitionList as $id => $value) {
            $items[$id] = $value;
        }

        $this->assertEquals([
            "id1" => $v1,
            "id2" => $v2,
            "id3" => $v3,
            "id4" => $v4,
        ], $items);
    }

    public function testShouldIterateElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', $v1 = new RawDefinition("value1"));
        $definitionList->add('id2', $v2 = new RawDefinition("value2"));

        $items = [];
        foreach ($definitionList->getIterator() as $id => $value) {
            $items[$id] = $value;
        }

        $this->assertEquals([
            "id1" => $v1,
            "id2" => $v2,
        ], $items);
    }

    public function testShouldGetTaggedElementsFromDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', $def1 = (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2'));
        $definitionList->add('id2', $def2 = (new RawDefinition("value2"))->addTag('tag2'));
        $definitionList->add('id3', $def3 = (new RawDefinition("value3"))->addTag('tag1'));
        $definitionList->add('id4', $def4 = (new RawDefinition("value4"))->addTag('tag3'));
        $definitionList->add('id5', $def5 = (new RawDefinition("value5")));

        $tag1 = $definitionList->getTagged('tag1');
        $tag2 = $definitionList->getTagged('tag2');
        $tag3 = $definitionList->getTagged('tag3');

        $this->assertCount(2, $tag1);
        $this->assertCount(2, $tag2);
        $this->assertCount(1, $tag3);

        foreach ($tag1 as $id => $definition) {
            $this->assertTrue(in_array($id, ['id1', 'id3'], true));
            $this->assertTrue(in_array($definition, [$def1, $def3], true));
        }
    }

    public function testShouldCheckTaggedElementsOnDefinitionList()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2'));
        $definitionList->add('id2', (new RawDefinition("value2"))->addTag('tag2'));
        $definitionList->add('id3', (new RawDefinition("value3"))->addTag('tag1'));
        $definitionList->add('id4', (new RawDefinition("value4"))->addTag('tag3'));
        $definitionList->add('id5', (new RawDefinition("value5")));

        $this->assertTrue($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag2'));
        $this->assertTrue($definitionList->hasTag('tag3'));
        $this->assertFalse($definitionList->hasTag('tag4'));
    }

    public function testShouldCheckTaggedElementsOnDefinitionListAfterReplace()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2'));
        $definitionList->add('id2', (new RawDefinition("value1"))->addTag('tag1')->addTag('tag3'));
        // replace definition without tags
        $definitionList->add('id1', (new RawDefinition("value1")));

        $this->assertTrue($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag3'));
        $this->assertFalse($definitionList->hasTag('tag2'));
    }

    public function testShouldCheckTaggedElementsOnDefinitionListAfterDelete()
    {
        $definitionList = new DefinitionList();
        $definitionList->add('id1', (new RawDefinition("value1"))->addTag('tag1')->addTag('tag2'));
        $definitionList->add('id2', (new RawDefinition("value1"))->addTag('tag2')->addTag('tag3'));
        $definitionList->delete('id1');

        $this->assertFalse($definitionList->hasTag('tag1'));
        $this->assertTrue($definitionList->hasTag('tag2'));
        $this->assertTrue($definitionList->hasTag('tag3'));
    }
}
