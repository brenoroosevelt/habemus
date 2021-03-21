<?php
declare(strict_types=1);

namespace Habemus\Test\Util;

use Habemus\Exception\NotFoundException;
use Habemus\Test\TestCase;
use Habemus\Utility\Lists\KeyValuePriorityList;
use http\Exception;

class KeyValuePriorityListTest extends TestCase
{
    public function newKeyValuePriorityList()
    {
        return new class {
            use KeyValuePriorityList;
        };
    }

    public function testShouldKeyValuePriorityListCreateEmptyList()
    {
        $list = $this->newKeyValuePriorityList();
        $this->assertTrue($list->isEmpty());
        $this->assertEquals(0, $list->count());
        $this->assertNull($list->getHighestPriority());
        $this->assertNull($list->getLowestPriority());
    }

    public function testShouldKeyValuePriorityListAddGetElements()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 2);
        $list->set(2, 'B', 3);

        $this->assertTrue($list->has(1));
        $this->assertTrue($list->has(2));
        $this->assertEquals('A', $list->get(1));
        $this->assertEquals('B', $list->get(2));
        $this->assertEquals(2, $list->count());
        $this->assertFalse($list->isEmpty());

        $this->assertEquals(3, $list->getLowestPriority());
        $this->assertEquals(2, $list->getHighestPriority());
    }

    public function testShouldKeyValuePriorityGetErrorIfNotFound()
    {
        $list = $this->newKeyValuePriorityList();
        $this->expectException(NotFoundException::class);
        $list->get(1);
    }

    public function testShouldKeyValuePriorityListReplaceAnElementById()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 1);
        $list->set(1, 'B', 2);
        $this->assertEquals(1, $list->count());
    }

    public function testShouldKeyValuePriorityListCountAfterDelete()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 1);
        $list->set(2, 'B', 2);
        $list->set(3, 'C', 2);
        $list->set(3, 'E', 2);
        $list->delete(2);
        $this->assertEquals(2, $list->count());
    }

    public function testShouldGetPrioritizedElements()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 2);
        $list->set(2, 'B', 3);
        $list->set(3, 'C', 1);
        $list->set(4, 'D', 5);
        $list->set(5, 'E', 4);
        $expected = ['C', 'A', 'B', 'E', 'D'];
        $i=0;
        foreach ($list->toArray() as $item) {
            $this->assertEquals($expected[$i++], $item);
        }
    }

    public function testShouldKeyValuePriorityListReturnsPrioritizedElementsAfterReplace()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 2);
        $list->set(2, 'B', 3);
        $list->set(3, 'C', 1);
        $list->set(4, 'D', 5);
        $list->set(5, 'E', 4);
        $list->set(1, 'X', 6); // replace
        $list->set(4, 'Y', 2); // replace

        $expected = ['C', 'Y', 'B', 'E', 'X'];
        $i=0;
        foreach ($list->toArray() as $item) {
            $this->assertEquals($expected[$i++], $item);
        }
    }

    public function testShouldKeyValuePriorityListReturnsPrioritizedElementsAfterDelete()
    {
        $list = $this->newKeyValuePriorityList();
        $list->set(1, 'A', 2);
        $list->set(2, 'B', 3);
        $list->set(3, 'C', 1);
        $list->set(4, 'D', 5);
        $list->set(5, 'E', 4);
        $list->set(1, 'X', 6); // replace
        $list->delete(4); // delete

        $expected = ['C', 'B', 'E', 'X'];
        $i=0;
        foreach ($list->toArray() as $item) {
            $this->assertEquals($expected[$i++], $item);
        }
    }
}
