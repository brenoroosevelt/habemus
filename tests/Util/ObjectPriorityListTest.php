<?php
declare(strict_types=1);

namespace Habemus\Test\Util;

use Habemus\Test\TestCase;
use Habemus\Util\Lists\ObjectPriorityList;
use InvalidArgumentException;
use stdClass;

class ObjectPriorityListTest extends TestCase
{
    public function testShouldCreateEmptyObjectPriorityList()
    {
        $list = new ObjectPriorityList();
        $this->assertTrue($list->isEmpty());
    }

    public function testShouldObjectPriorityListAddGetElement()
    {
        $list = new ObjectPriorityList();
        $item = new stdClass();
        $list->add($item, 1);
        $this->assertTrue($list->has($item));
        $this->assertEquals(1, $list->count());
        $this->assertFalse($list->isEmpty());
    }

    public function testShouldObjectPriorityAcceptOnlyObjectsWhenAdd()
    {
        $list = new ObjectPriorityList();
        $this->expectException(InvalidArgumentException::class);
        $list->add("string", 1);
    }

    public function testShouldObjectPriorityAcceptOnlyObjectsWhenDelete()
    {
        $list = new ObjectPriorityList();
        $this->expectException(InvalidArgumentException::class);
        $list->delete("string");
    }

    public function testShouldObjectPriorityAcceptOnlyObjectsWhenCheck()
    {
        $list = new ObjectPriorityList();
        $this->expectException(InvalidArgumentException::class);
        $list->has("string");
    }

    public function testShouldObjectPriorityListDeleteElement()
    {
        $list = new ObjectPriorityList();
        $item = new stdClass();
        $list->add($item, 1);
        $list->delete($item);
        $this->assertFalse($list->has($item));
        $this->assertEquals(0, $list->count());
        $this->assertTrue($list->isEmpty());
    }

    public function testShouldIterateObjectPriorityList()
    {
        $list = new ObjectPriorityList();
        $item1 = new stdClass();
        $item2 = new stdClass();
        $item3 = new stdClass();
        $list->add($item1, 1);
        $list->add($item2, 1);
        $list->add($item3, 1);
        foreach ($list as $item) {
            $this->assertTrue(in_array($item, [$item1, $item2, $item3], true));
        }
    }

    public function testShouldIterateObjectPriorityListInPriorityOrder()
    {
        $list = new ObjectPriorityList();
        $item1 = new stdClass();
        $item2 = new stdClass();
        $item3 = new stdClass();
        $list->add($item1, 3);
        $list->add($item2, 1);
        $list->add($item3, 2);
        $expected = [$item2, $item3, $item1];
        $i=0;
        foreach ($list as $item) {
            $this->assertEquals($expected[$i++], $item);
        }
        $this->assertEquals(3, $list->getLowestPriority());
        $this->assertEquals(1, $list->getHighestPriority());
    }
}
